<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Tests\Unit\SurePay;

use Faker\Factory;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\Enums\AccountStatus;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\Exceptions\SurePayRepositoryException;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\SurePayClient;
use MinVWS\DUSi\Shared\Tests\TestCase;
use MinVWS\DUSi\Shared\Tests\Unit\SurePay\Fakes\AccessTokenResponseFake;
use MinVWS\DUSi\Shared\Tests\Unit\SurePay\Fakes\CheckOrganisationsAccountResponseFake;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;

/**
 * @group sure-pay
 */
class SurePayClientTest extends TestCase
{
    private array $container = [];

    /**
     * @param array $mockResponses [new Response(200), new Response(400)]
     * @return SurePayClient
     */
    public function initSUT(array $mockResponses = [new Response()]): SurePayClient
    {
        $this->container = [];
        $history = Middleware::history($this->container);

        $mock = new MockHandler($mockResponses);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);

        return new SurePayClient(
            client: new Client([
                'base_uri' => '',
                'handler' => $handlerStack
            ])
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();

        Config::set('surepay_api', [
            'key' => 'mock_key',
            'secret' => 'mock_secret',
            'endpoint' => 'http://example.com',
            'debug' => false,
            'request_timeout_seconds' => 3,
            'connect_timeout_seconds' => 3,
            'endpoint_request_accesstoken' => 'oauth/client_credential/accesstoken',
            'endpoint_check_organisations' => 'account/check/organisations',
        ]);
    }

    /**
     * Test that the constructor throws a SurePayServiceException if the config is not correct.
     */
    public function testConstructorThrowsSurePayServiceExceptionForIncorrectConfig()
    {
        // Arrange & Assert
        $this->expectException(SurePayRepositoryException::class);

        Config::set('surepay_api', [
            'key' => null,
            'secret' => null,
            'endpoint' => null,
            'debug' => null,
            'request_timeout_seconds' => null,
            'connect_timeout_seconds' => null,
            'endpoint_request_accesstoken' => null,
            'endpoint_check_organisations' => null,
        ]);

        // Act
        $this->initSUT();
    }

    /**
     * Test that checkOrganisationsAccount throws SurePayServiceException if fetchAccessToken fails.
     * @throws ValidationException
     */
    public function testCheckOrganisationsAccountThrowsExceptionOnFetchAccessTokenFailure()
    {
        // Arrange & Assert
        $this->expectException(SurePayRepositoryException::class);

        $body401 = '{"ErrorCode": "invalid_request", "Error": "Invalid client id : abc. ClientId is Invalid"}';
        $sut = $this->initSUT([new Response(status: 401, body: $body401)]);

        // Act
        $sut->checkOrganisationsAccount($this->faker->name, $this->faker->iban('NL'));
    }

    /**
     * Test that checkOrganisationsAccount fails if input is invalid.
     * @throws ValidationException
     */
    public function testCheckOrganisationsAccountFailsForInvalidInput()
    {
        // Arrange & Assert
        $this->expectException(SurePayRepositoryException::class);

        $fetchAccessTokenResponse = new Response(status: 200, body: json_encode(AccessTokenResponseFake::build()));
        $checkOrganisationsAccountResponse = new Response(status: 500);

        $sut = $this->initSUT([
            $fetchAccessTokenResponse,
            $checkOrganisationsAccountResponse
        ]);

        // Act
        $sut->checkOrganisationsAccount('', $this->faker->iban('NL'));
    }

    /**
     * Test that checkOrganisationsAccount calls client->request and returns CheckOrganisationsResponse.
     * @throws ValidationException
     */
    public function testCheckOrganisationsAccountCallsClientRequestAndReturnsResponse()
    {
        $fetchAccessTokenResponse = new Response(status: 200, body: json_encode(AccessTokenResponseFake::build()));
        $checkOrganisationsAccountResponse = new Response(
            status: 200,
            body: json_encode(CheckOrganisationsAccountResponseFake::build())
        );

        $sut = $this->initSUT([
            $fetchAccessTokenResponse,
            $checkOrganisationsAccountResponse
        ]);

        // Act
        $response = $sut->checkOrganisationsAccount($this->faker->name, $this->faker->iban);

        assertEquals($response->account->status, AccountStatus::Active);
    }

    /**
     * Tests when accesstoken is available it is reused.
     * @throws ValidationException
     */
    public function testAccessTokenReuse()
    {
        $sut = $this->initSUT([
            new Response(status: 200, body: json_encode(AccessTokenResponseFake::build())),
            new Response(status: 200, body: json_encode(CheckOrganisationsAccountResponseFake::build())),
            new Response(status: 200, body: json_encode(CheckOrganisationsAccountResponseFake::build())),
        ]);

        // Act
        $response = $sut->checkOrganisationsAccount($this->faker->name, $this->faker->iban('NL'));
        $twoCallsMessage = 'Expect 2 calls, 1 for fetching the token second for checkOrganisationsAccount.';
        assertCount(2, $this->container, $twoCallsMessage);
        assertEquals($response->account->status, AccountStatus::Active);

        $response = $sut->checkOrganisationsAccount($this->faker->name, $this->faker->iban('NL'));
        $threeCallsMessage = 'Expect 3 calls, 1 for fetching the token and 2 for checkOrganisationsAccount.
        In the second call token should be reused';
        assertCount(3, $this->container, $threeCallsMessage);
        assertEquals($response->account->status, AccountStatus::Active);
    }
}
