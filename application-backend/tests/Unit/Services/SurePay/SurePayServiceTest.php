<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Tests\Unit\Services\SurePay;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use MinVWS\DUSi\Application\Backend\Services\SurePay\Exceptions\SurePayServiceException;
use MinVWS\DUSi\Application\Backend\Services\SurePay\SurePayService;
use MinVWS\DUSi\Application\Backend\Tests\TestCase;
use MinVWS\DUSi\Application\Backend\Tests\Unit\Services\SurePay\Fakes\AccesstokenResponseFake;
use MinVWS\DUSi\Application\Backend\Tests\Unit\Services\SurePay\Fakes\CheckOrganisationsAccountResponseFake;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;

class SurePayServiceTest extends TestCase
{
    private array $container = [];

    /**
     * @param array $mockResponses [new Response(200), new Response(400)]
     * @return SurePayService
     */
    public function initSUT(array $mockResponses = [new Response()]): SurePayService
    {
        $this->container = [];
        $history = Middleware::history($this->container);

        $mock = new MockHandler($mockResponses);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);

        return new SurePayService(
            client: new Client([
                'base_uri' => '',
                'handler' => $handlerStack
            ])
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('surepay_api', [
            'key' => env('SUREPAY_KEY'),
            'secret' => env('SUREPAY_SECRET'),
            'endpoint' => env('SUREPAY_ENDPOINT'),
            'debug' => env('SUREPAY_DEBUG', false),
            'request_timeout_seconds' => env('SUREPAY_REQUEST_TIMEOUT_SECONDS', 3),
            'connect_timeout_seconds' => env('SUREPAY_CONNECT_TIMEOUT_SECONDS', 3),
            'endpoint_request_accesstoken' => env('SUREPAY_ENDPOINT_REQUEST_ACCESSTOKEN'),
            'endpoint_check_organisations' => env('SUREPAY_ENDPOINT_CHECK_ORGANISATIONS'),
        ]);
    }

    /**
     * Test that the constructor throws a SurePayServiceException if the config is not correct.
     */
    public function testConstructorThrowsSurePayServiceExceptionForIncorrectConfig()
    {
        // Arrange & Assert
        $this->expectException(SurePayServiceException::class);

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
        $this->expectException(SurePayServiceException::class);

        $body401 = '{"ErrorCode": "invalid_request", "Error": "Invalid client id : AVD7ztXReEYyjpLFkkPiZpLEjeF2aYAz. ClientId is Invalid"}';
        $sut = $this->initSUT([new Response(status: 401, body: $body401)]);

        // Act
        $sut->checkOrganisationsAccount('Valdosta Textiles', 'NL42RABO2288983183');
    }

    /**
     * Test that checkOrganisationsAccount fails if input is invalid.
     * @throws ValidationException
     */
    public function testCheckOrganisationsAccountFailsForInvalidInput()
    {
        // Arrange & Assert
        $this->expectException(SurePayServiceException::class);

        $fetchAccessTokenResponse = new Response(status: 200, body: json_encode(AccesstokenResponseFake::build()));
        $checkOrganisationsAccountResponse = new Response(status: 500);

        $sut = $this->initSUT([
            $fetchAccessTokenResponse,
            $checkOrganisationsAccountResponse
        ]);

        // Act
        $sut->checkOrganisationsAccount('', 'NL42RABO2288983183');
    }

    /**
     * Test that checkOrganisationsAccount calls client->request and returns CheckOrganisationsResponse.
     * @throws ValidationException
     */
    public function testCheckOrganisationsAccountCallsClientRequestAndReturnsResponse()
    {
        $fetchAccessTokenResponse = new Response(status: 200, body: json_encode(AccesstokenResponseFake::build()));
        $checkOrganisationsAccountResponse = new Response(status: 200, body: json_encode(new CheckOrganisationsAccountResponseFake()));

        $sut = $this->initSUT([
            $fetchAccessTokenResponse,
            $checkOrganisationsAccountResponse
        ]);

        // Act
        $response = $sut->checkOrganisationsAccount('John Paul Waldo', 'NL87MOYO9876543212');

        assertEquals($response->status, 'ACTIVE');
    }

    /**
     * Tests when accesstoken is available it is reused.
     * @throws ValidationException
     */
    public function testAccessTokenReuse()
    {
        $sut = $this->initSUT([
            new Response(status: 200, body: json_encode(AccesstokenResponseFake::build())),
            new Response(status: 200, body: json_encode(new CheckOrganisationsAccountResponseFake())),
            new Response(status: 200, body: json_encode(new CheckOrganisationsAccountResponseFake())),
        ]);

        // Act
        $response = $sut->checkOrganisationsAccount('John Paul Waldo', 'NL87MOYO9876543212');
        assertCount(2, $this->container, 'Expect 2 calls, 1 for fetching the token second for checkOrganisationsAccount.');
        assertEquals($response->status, 'ACTIVE');

        $response = $sut->checkOrganisationsAccount('John Paul Waldo', 'NL87MOYO9876543212');
        assertCount(3, $this->container,
            'Expect 3 calls, 1 for fetching the token and 2 for checkOrganisationsAccount. In the second call token should be reused');
        assertEquals($response->status, 'ACTIVE');
    }
}
