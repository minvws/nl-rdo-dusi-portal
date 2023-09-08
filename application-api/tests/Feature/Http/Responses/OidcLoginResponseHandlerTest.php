<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Tests\Feature\Http\Responses;

use Illuminate\Http\RedirectResponse;
use MinVWS\Codable\Decoding\Decoder;
use MinVWS\DUSi\Application\API\Http\Responses\OidcLoginResponseHandler;
use MinVWS\DUSi\Application\API\Services\Oidc\OidcUserLoa;
use MinVWS\DUSi\Application\API\Tests\TestCase;

class OidcLoginResponseHandlerTest extends TestCase
{
    public function testWithoutMinimumLoa(): void
    {
        $responseHandler = new OidcLoginResponseHandler(
            frontendBaseUrl: 'https://example.com',
            decoder: new Decoder(),
            minimumLoa: null,
        );

        $redirectResponse = $responseHandler->handleLoginResponse((object) [
            'bsn' => '1234567890',
        ]);

        $this->assertEquals($redirectResponse::class, RedirectResponse::class);
        $this->assertEquals($redirectResponse->getTargetUrl(), 'https://example.com/login-callback');
    }

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
        );

        $redirectResponse = $responseHandler->handleLoginResponse((object) [
            'bsn' => '1234567890',
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
                actual: 'https://example.com/login-callback?error=minimum_loa',
            );
        }
    }

    public static function loaDataProvider(): array
    {
        return [
            'without minimum loa and user has null loa' => [null, null, true],
             'without minimum loa and user has substantial loa' => [null, OidcUserLoa::SUBSTANTIAL, true],
            'with substantial loa and user has substantial loa' => [
                OidcUserLoa::SUBSTANTIAL,
                OidcUserLoa::SUBSTANTIAL,
                true
            ],
            'with substantial loa and user has null loa' => [OidcUserLoa::SUBSTANTIAL, null, false],
            'with high loa and user has substantial loa' => [OidcUserLoa::HIGH, OidcUserLoa::SUBSTANTIAL, false],
        ];
    }

    /**
     * @dataProvider digidMockEnabledDataProvider
     */
    public function testWithDigidMockEnabled(OidcUserLoa $minimumLoa): void
    {
        $responseHandler = new OidcLoginResponseHandler(
            frontendBaseUrl: 'https://example.com',
            decoder: new Decoder(),
            minimumLoa: $minimumLoa,
            digidMockEnabled: true
        );

        $redirectResponse = $responseHandler->handleLoginResponse((object) [
            'bsn' => '1234567890',
        ]);

        $this->assertEquals($redirectResponse::class, RedirectResponse::class);
        $this->assertEquals($redirectResponse->getTargetUrl(), 'https://example.com/login-callback');
    }


    public static function digidMockEnabledDataProvider(): array
    {
        return array_map(fn ($v) => [$v], OidcUserLoa::cases());
    }
}
