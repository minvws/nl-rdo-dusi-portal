<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Tests\Feature\Http\Responses;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use MinVWS\Codable\Decoding\Decoder;
use MinVWS\DUSi\Application\API\Http\Responses\OidcLoginResponseHandler;
use MinVWS\DUSi\Application\API\Services\Oidc\OidcUserLoa;
use MinVWS\DUSi\Application\API\Tests\TestCase;
use MinVWS\Logging\Laravel\LogService;

class OidcLoginResponseHandlerTest extends TestCase
{
    /**
     * @dataProvider loaDataProvider
     * @param OidcUserLoa|null $minimumLoa
     * @param OidcUserLoa|null $userLoa
     * @param bool $success
     * @return void
     */
    public function testLoaTest(?OidcUserLoa $minimumLoa, ?OidcUserLoa $userLoa, bool $success): void
    {
        $responseHandler = new OidcLoginResponseHandler(
            frontendBaseUrl: 'https://example.com',
            decoder: new Decoder(),
            minimumLoa: $minimumLoa,
            logger: new LogService(),
        );

        $redirectResponse = $responseHandler->handleLoginResponse((object) [
            'bsn' => '1234567890',
            'session_id' => 'test-session-id',
            'loa_authn' => $userLoa?->value,
        ]);

        $this->assertEquals($redirectResponse::class, RedirectResponse::class);

        if ($success) {
            $this->assertEquals(
                expected: $redirectResponse->getTargetUrl(),
                actual: 'https://example.com/login-callback',
            );
        } else {
            $this->assertEquals(
                expected: $redirectResponse->getTargetUrl(),
                actual: 'https://example.com/login-callback?error=minimum_loa&minimum_loa='
                . $minimumLoa->code() . '&current_loa=' . $userLoa->code(),
            );
        }
    }

    public static function loaDataProvider(): array
    {
        return [
            'with substantial loa and user has substantial loa' => [
                OidcUserLoa::SUBSTANTIAL,
                OidcUserLoa::SUBSTANTIAL,
                true
            ],
            'with high loa and user has substantial loa' => [OidcUserLoa::HIGH, OidcUserLoa::SUBSTANTIAL, false],
        ];
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function testInvalidLoginResponse(array $response): void
    {
        $responseHandler = new OidcLoginResponseHandler(
            frontendBaseUrl: 'https://example.com',
            decoder: new Decoder(),
            minimumLoa: OidcUserLoa::SUBSTANTIAL,
            logger: new LogService(),
        );

        $this->expectException(AuthorizationException::class);

        $responseHandler->handleLoginResponse((object)$response);
    }

    public static function invalidDataProvider(): array
    {
        return [
            'missing_data' => [[]],
            'missing_loa_authn' => [['bsn' => '123456789']],
            'missing_bsn' => [['loa_authn' => OidcUserLoa::SUBSTANTIAL->value]],
            'missing_session_id' => [['bsn' => '123456789', 'loa_authn' => OidcUserLoa::SUBSTANTIAL->value]],
            'invalid_loa_authn' => [['bsn' => '123456789', 'loa_authn' => 'does-not-exist', 'session_id' => 'test-session-id']],
        ];
    }
}
