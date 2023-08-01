<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Models\PortalUser;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use MinVWS\OpenIDConnectLaravel\Http\Responses\LoginResponseHandlerInterface;
use Symfony\Component\HttpFoundation\Response;

class OidcLoginResponseHandler implements LoginResponseHandlerInterface
{
    /**
     * @throws AuthorizationException
     */
    public function handleLoginResponse(object $userInfo): Response
    {
        $user = PortalUser::deserializeFromObject($userInfo);
        if ($user === null) {
            throw new AuthorizationException("Empty userinfo");
        }
        // TODO: Log login to Calvin?
        Auth::setUser($user);
        return new RedirectResponse(config('app.frontend_base_url') . '/login-callback');
    }
}
