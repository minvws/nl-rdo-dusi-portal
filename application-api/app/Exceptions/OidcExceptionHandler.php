<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Exceptions;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Jumbojett\OpenIDConnectClientException;
use MinVWS\OpenIDConnectLaravel\Services\ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;

class OidcExceptionHandler extends ExceptionHandler
{
    /**
     * Called when url contains query parameter error.
     * For example user is sent back from idp with error=login_cancelled.
     * @param OpenIDConnectClientException $exception
     * @return Response
     */
    protected function handleRequestError(OpenIDConnectClientException $exception): Response
    {
        $error = $this->getErrorParamFromRequest();
        switch ($error) {
            // If authentication flow cancelled from chosen authentication provider
            case 'login_required':
                return $this->getFrontendRedirectResponse('login_cancelled');
        }

        return $this->default400Response($exception);
    }

    /**
     * Called when url contains query parameter code and state, and state does not match with the value from session.
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param OpenIDConnectClientException $exception
     * @return Response
     */
    protected function handleUnableToDetermineState(OpenIDConnectClientException $exception): Response
    {
        return $this->getFrontendRedirectResponse('login_failed');
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param OpenIDConnectClientException $exception
     * @return Response
     */
    protected function defaultResponse(OpenIDConnectClientException $exception): Response
    {
        return $this->getFrontendRedirectResponse('login_failed');
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param OpenIDConnectClientException $exception
     * @return Response
     */
    protected function defaultResponseGenericException(Exception $exception): Response
    {
        return $this->getFrontendRedirectResponse('login_failed');
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param OpenIDConnectClientException $exception
     * @return Response
     */
    protected function default400Response(OpenIDConnectClientException $exception): Response
    {
        return $this->getFrontendRedirectResponse('login_failed');
    }

    protected function getErrorParamFromRequest(): ?string
    {
        $request = request();
        if (!($request instanceof Request)) {
            return null;
        }

        $error = $request->query('error');
        if (!is_string($error)) {
            return null;
        }

        return $error;
    }

    protected function getFrontendRedirectResponse(string $error): RedirectResponse
    {
        return new RedirectResponse(config('app.frontend_base_url') . '/login-callback' . '?error=' . $error);
    }
}
