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
        private readonly string $frontendBaseUrl,
        private readonly Decoder $decoder,
        private readonly OidcUserLoa $minimumLoa
    ) {
    }

    /**
     * @throws AuthorizationException
     */
    public function handleLoginResponse(object $userInfo): Response
    {
        try {
            $user = $this->decoder->decode($userInfo)->decodeObject(PortalUser::class);
        } catch (CodableException $e) {
            Log::error("Trying to build an PortalUser from userinfo failed", [$e]);
            throw new AuthorizationException("Invalid user info", previous: $e);
        }

        if (!OidcUserLoa::isEqualOrHigher($this->minimumLoa, $user->loaAuthn)) {
            return new RedirectResponse($this->frontendBaseUrl . '/login-callback?' . http_build_query([
                'error' => 'minimum_loa',
                'minimum_loa' => $this->minimumLoa->code(),
                'current_loa' => $user->loaAuthn->code(),
            ]));
        }

        // TODO: Log login to Calvin?
        Auth::setUser($user);
        return new RedirectResponse($this->frontendBaseUrl . '/login-callback');
    }
}
