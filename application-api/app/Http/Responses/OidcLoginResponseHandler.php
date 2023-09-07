<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Http\Responses;

use MinVWS\DUSi\Application\API\Models\PortalUser;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
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
        $user = PortalUser::deserializeFromObject($userInfo);
        if ($user === null) {
            throw new AuthorizationException("Empty userinfo");
        }

        if (!OidcUserLoa::isEqualOrHigher($this->minimumLoa, $user->loaAuthn)) {
            return new RedirectResponse($this->frontendBaseUrl . '/login-callback?error=minimum_loa');
        }

        // TODO: Log login to Calvin?
        Auth::setUser($user);
        return new RedirectResponse($this->frontendBaseUrl . '/login-callback');
    }
}
