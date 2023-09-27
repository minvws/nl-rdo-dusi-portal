<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\SurePay;

use Carbon\Carbon;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use MinVWS\DUSi\Shared\Application\Services\SurePay\DTO\AccesstokenResponse;
use MinVWS\DUSi\Shared\Application\Services\SurePay\DTO\CheckOrganisationsAccountResponse;
use MinVWS\DUSi\Shared\Application\Services\SurePay\DTO\CheckOrganisationsRequest;
use MinVWS\DUSi\Shared\Application\Services\SurePay\Exceptions\SurePayServiceException;

/**
 * ===========================================================================
 * ======================= IMPORTANT WHEN TESTING: ============================
 * ===========================================================================
 *
 * Please note that the sandbox environment is only available from 07:00-20:00 on working days.
 * Outside of these hours you’d receive a timeout error. Also, be aware that the IP
 * needs to be whitelisted before you can initiate an API call.
 *
 */
class SurePayService
{
    private mixed $config;
    private array $baseRequestOptions;
    private ?AccesstokenResponse $accessToken;

    /**
     * @param ClientInterface $client
     */
    public function __construct(protected ClientInterface $client)
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
     * @return AccesstokenResponse
     * @throws ValidationException
     */
    private function fetchAccessToken(): AccesstokenResponse
    {
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

            return AccesstokenResponse::fromJson($response->getBody()->getContents());
        } catch (GuzzleException $e) {
            throw new SurePayServiceException('Unable to get accesstoken', 0, $e);
        }
    }

    /**
     * @param string $accountOwner
     * @param string $accountNumber
     * @param string $accountType
     * @return CheckOrganisationsAccountResponse
     * @throws ValidationException
     */
    public function checkOrganisationsAccount(
        string $accountOwner,
        string $accountNumber,
        string $accountType = 'IBAN'
    ): CheckOrganisationsAccountResponse {
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
                            'Authorization' => sprintf('Bearer %s', $this->getAccessToken()),
                        ],
                        RequestOptions::JSON => CheckOrganisationsRequest
                            ::build($accountOwner, $accountNumber, $accountType)
                            ->toArray()
                    ]
                )
            );

            return CheckOrganisationsAccountResponse::fromJson($response->getBody()->getContents());
        } catch (GuzzleException $e) {
            throw new SurePayServiceException('Unable to get accesstoken', 0, $e);
        }
    }

    /**
     * @return void
     * @throws SurePayServiceException
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
            throw new SurePayServiceException(
                'SurePay API config invalid it must be set in the environment config.',
                0,
                $e
            );
        }
    }

    /**
     * Returns accesstoken. An existing one if valid or a new one is fetched
     * @return string
     * @throws ValidationException
     */
    private function getAccessToken(): string
    {
        if ($this->shouldFetchToken()) {
            $this->accessToken = $this->fetchAccessToken();
        }

        if (!isset($this->accessToken->accessToken)) {
            throw new SurePayServiceException('Accesstoken not set');
        }

        return $this->accessToken->accessToken;
    }

    /**
     * Checks if we already have a valid accesstoken or if one needs to be fetched from surepay
     * @return bool
     */
    private function shouldFetchToken(): bool
    {
        if (!isset($this->accessToken)) {
            return true;
        }

        // -100 seconds to be on the safe side.
        $expiresInSeconds = ($this->accessToken->expiresIn - 100);
        $tokenExpiresAt = Carbon::createFromTimestamp($this->accessToken->issuedAt)->addSeconds($expiresInSeconds);

        return $tokenExpiresAt->isPast();
    }
}
