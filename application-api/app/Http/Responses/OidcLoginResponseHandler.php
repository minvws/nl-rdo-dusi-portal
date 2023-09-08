<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Http\Responses;

use MinVWS\Codable\Decoding\Decoder;
use MinVWS\Codable\Exceptions\CodableException;
use MinVWS\DUSi\Application\API\Models\PortalUser;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use MinVWS\DUSi\Application\API\Services\Oidc\OidcUserLoa;
use MinVWS\OpenIDConnectLaravel\Http\Responses\LoginResponseHandlerInterface;
use Symfony\Component\HttpFoundation\Response;

class OidcLoginResponseHandler implements LoginResponseHandlerInterface
{
    public function __construct(
        protected string $frontendBaseUrl,
        protected ?OidcUserLoa $minimumLoa = null,
    ) {
    }

    /**
     * @throws AuthorizationException
     */
    public function handleLoginResponse(object $userInfo): Response
    {
        try {
            $user = (new Decoder())->decode($userInfo)->decodeObject(PortalUser::class);
        } catch (CodableException $e) {
            Log::error("Trying to build an PortalUser from userinfo failed", [$e]);
            throw new AuthorizationException("Invalid user info", previous: $e);
        }

        if (config('auth.digid_mock_enabled') && $user->loaAuthn === null) {
            // digid mock doesn't provide the loaAuthn value
            $user->loaAuthn = $this->minimumLoa;
        }

        if (!OidcUserLoa::isEqualOrHigher($this->minimumLoa, $user->loaAuthn)) {
            return new RedirectResponse($this->frontendBaseUrl . '/login-callback?error=minimum_loa');
        }

        // TODO: Log login to Calvin?
        Auth::setUser($user);
        return new RedirectResponse($this->frontendBaseUrl . '/login-callback');
    }
}
