<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Repositories\SurePay;

use Carbon\CarbonImmutable;
use Exception;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\AccessTokenResponse;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\CheckOrganisationsAccountResponse;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\CheckOrganisationsRequest;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\Exceptions\SurePayInvalidAccessTokenResponse;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\Exceptions\SurePayMaxRetryException;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\Exceptions\SurePayRepositoryException;
use Psr\Log\LoggerInterface;

/**
 * ===========================================================================
 * ======================= IMPORTANT WHEN TESTING: ============================
 * ===========================================================================
 *
 * Please note that the sandbox environment is only available from 07:00-20:00 on working days.
 * Outside of these hours you’d receive a timeout error. Also, be aware that the IP
 * needs to be whitelisted before you can initiate an API call.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SurePayClient
{
    private mixed $config;
    private array $baseRequestOptions;
    private ?SurePayAccessToken $accessToken = null;

    /**
     * We want to be on the safe side and not use an expired access token.
     * So we refresh the access token 100 seconds before it expires.
     *
     * @var int
     */
    protected int $accessTokenExpiresInLeeWay = 100;

    /**
     * @param ClientInterface $client
     */
    public function __construct(protected ClientInterface $client, protected ?LoggerInterface $logger = null)
    {
        $this->config = config('surepay_api');

        $this->validateConfigOrThrow();

        $this->baseRequestOptions = [
            RequestOptions::CONNECT_TIMEOUT => $this->config['connect_timeout_seconds'],
            RequestOptions::TIMEOUT => $this->config['request_timeout_seconds'],
            RequestOptions::DEBUG => $this->config['debug']
        ];
    }

    /**
     * A unique correlation ID should be sent for every request.
     * Validations: ^[a-zA-Z0-9\-]{1,70}
     *
     * @return string uuid
     */
    private function getCorrelationId(): string
    {
        return Str::uuid()->toString();
    }

    /**
     *  You need an access token to use the API. To obtain an access token you need to call the oAuth API. We only
     *  support the grant_type: client_credentials.
     *  The oAuth 2.0 specifications recommend passing the API key and secret values as an HTTP-Basic Authentication
     *  header.
     *
     * More information can be found in: SurePay API Authentication v1.4 - ORG.pdf
     *
     * @return SurePayAccessToken
     * @throws SurePayRepositoryException
     */
    private function fetchAccessToken(): SurePayAccessToken
    {
        $this->logger?->info('Fetching SurePay access token');

        try {
            $response = $this->client->request(
                'POST',
                $this->config['endpoint_request_accesstoken'],
                array_merge(
                    $this->baseRequestOptions,
                    [
                        RequestOptions::HEADERS => [
                            'Content-Type' => 'application/x-www-form-urlencoded',
                        ],
                        RequestOptions::AUTH => [
                            $this->config['key'],
                            $this->config['secret']
                        ],
                        RequestOptions::FORM_PARAMS => [
                            'grant_type' => 'client_credentials',
                        ],
                    ]
                )
            );

            $accessTokenResponse = AccessTokenResponse::fromJson($response->getBody()->getContents());
            $accessToken = $this->parseAccessTokenFromAccessTokenResponse($accessTokenResponse);

            $this->logger?->info('Fetched SurePay access token', [
                'access_token' => [
                    'issued_at' => $accessToken->getIssuedAt(),
                    'expires_at' => $accessToken->getExpiresAt(),
                ]
            ]);

            return $accessToken;
        } catch (GuzzleException $e) {
            $this->logger?->error('Unable to fetch SurePay access token', [
                'exception' => $e,
                'response' => $this->getExceptionResponseDataForLog($e),
            ]);

            throw new SurePayRepositoryException(message: 'Unable to get accesstoken', previous: $e);
        } catch (SurePayInvalidAccessTokenResponse $e) {
            $this->logger?->error('Invalid SurePay access token response', [
                'exception' => $e,
            ]);

            throw new SurePayRepositoryException(message: 'Invalid access token response', previous: $e);
        }
    }

    /**
     * @param string $accountOwner
     * @param string $accountNumber
     * @param string $accountType
     * @return CheckOrganisationsAccountResponse
     * @throws SurePayRepositoryException
     * @throws SurePayMaxRetryException
     * @throws Exception
     */
    public function checkOrganisationsAccount(
        string $accountOwner,
        string $accountNumber,
        string $accountType = 'IBAN'
    ): CheckOrganisationsAccountResponse {
        $this->logger?->info('Checking SurePay IBAN');

        $maxRetries = $this->config['max_retries'] ?? 3;
        $retryCount = 0;
        $latestException = null;

        while ($retryCount < $maxRetries) {
            $accessToken = $this->getAccessToken();

            try {
                $response = $this->client->request(
                    'POST',
                    $this->config['endpoint_check_organisations'],
                    array_merge(
                        $this->baseRequestOptions,
                        [
                            RequestOptions::HEADERS => [
                                'charset' => 'utf-8',
                                'X-Correlation-Id' => $this->getCorrelationId(),
                                'Authorization' => sprintf('Bearer %s', $accessToken->getAccessToken()),
                            ],
                            RequestOptions::JSON => CheckOrganisationsRequest
                                ::build($accountOwner, $accountNumber, $accountType)
                                ->toArray()
                        ]
                    )
                );

                return CheckOrganisationsAccountResponse::fromJson($response->getBody()->getContents());
            } catch (ClientException $e) {
                $latestException = $e;
                $retryCount++;

                $response = $e->getResponse();

                // 401 - Unauthorised - Authorization header missing or invalid token
                if ($response->getStatusCode() === 401) {
                    $this->logger?->info(
                        'Checking SurePay IBAN failed because of access token unauthorized,'
                        . ' invalidating access token and retrying'
                    );
                    $this->logger?->info('We thought the SurePay access token is still valid', [
                        'access_token' => [
                            'issued_at' => $accessToken->getIssuedAt(),
                            'expired_at' => $accessToken->getExpiresAt(),
                        ]
                    ]);
                    $this->invalidateAccessToken();
                    continue;
                }

                // 429 - Too many requests - Response status code indicates the user has sent
                // too many requests in a given amount of time.
                if ($response->getStatusCode() === 429) {
                    $this->logger?->info('Checking SurePay IBAN failed because of too many requests, retrying');
                    sleep(random_int(1, 3));
                    continue;
                }

                $this->logger?->info('Checking SurePay IBAN failed because of bad request', [
                    'exception' => $e,
                    'response' => $this->getExceptionResponseDataForLog($e),
                ]);

                // All other 4xx errors are considered bad requests and do not need to be retried
                throw new SurePayRepositoryException(
                    message: 'Bad request for Checking SurePay IBAN',
                    previous: $e,
                );
            } catch (GuzzleException $e) {
                $latestException = $e;
                $retryCount++;

                $this->logger?->error('Unable to check SurePay IBAN', [
                    'exception' => $e,
                    'response' => $this->getExceptionResponseDataForLog($e),
                ]);

                sleep(random_int(1, 3));
            }
        }

        throw new SurePayMaxRetryException(
            message:  'Max retries exceeded for Checking SurePay IBAN without successful response',
            previous: $latestException,
            retries:  $maxRetries,
        );
    }

    /**
     * @return void
     * @throws SurePayRepositoryException
     */
    private function validateConfigOrThrow(): void
    {
        try {
            Validator::make($this->config, [
                    'key' => 'required',
                    'secret' => 'required',
                    'endpoint' => 'required|url',
                    'debug' => 'required|boolean',
                    'request_timeout_seconds' => 'required|integer',
                    'connect_timeout_seconds' => 'required|integer',
                    'endpoint_request_accesstoken' => 'required|doesnt_start_with:/,http',
                    'endpoint_check_organisations' => 'required|doesnt_start_with:/,http',
                ])->validate();
        } catch (ValidationException $e) {
            throw new SurePayRepositoryException(
                'SurePay API config invalid: ' . $e->getMessage(),
                previous: $e
            );
        }
    }

    /**
     * Returns access token. An existing one if valid or a new one is fetched
     * @return SurePayAccessToken
     * @throws SurePayRepositoryException
     */
    private function getAccessToken(): SurePayAccessToken
    {
        if ($this->accessToken === null || $this->accessToken->isExpired()) {
            $this->logger?->debug('No SurePay access token set or the access token is expired, fetching a new one', [
                'access_token_exists' =>  $this->accessToken !== null,
                'access_token_expired' => $this->accessToken?->isExpired()
            ]);

            $this->accessToken = $this->fetchAccessToken();
        }

        return $this->accessToken;
    }

    /**
     * According to the SurePay API documentation no PII is returned in the response body.
     *
     * For the check organisation endpoint we should receive a JSON body on 400 Bad Request
     *  with an errorCode and a message that we can check in the documentation.
     *
     * You can find the information in:
     *  SurePay API documentatie - ORG v2 - Account Check for Organisations v2.pdf
     *
     * @param GuzzleException $exception
     * @return array
     */
    protected function getExceptionResponseDataForLog(GuzzleException $exception): array
    {
        if (!($exception instanceof RequestException)) {
            return [];
        }

        $response = $exception->getResponse();
        if ($response === null) {
            return [];
        }

        $responseBody = $response->getBody()->getContents();
        $responseStatus = $response->getStatusCode();

        return [
            'status' => $responseStatus,
            'body' => $responseBody,
        ];
    }

    protected function invalidateAccessToken(): void
    {
        $this->accessToken = null;
    }

    protected function parseAccessTokenFromAccessTokenResponse(
        AccessTokenResponse $accessTokenResponse
    ): SurePayAccessToken {
        $issuedAt = CarbonImmutable::createFromTimestampMsUTC($accessTokenResponse->issuedAt);

        return new SurePayAccessToken(
            accessToken: $accessTokenResponse->accessToken,
            issuedAt: $issuedAt,
            expiresAt: $issuedAt->addSeconds($accessTokenResponse->expiresIn - $this->accessTokenExpiresInLeeWay),
        );
    }
}
