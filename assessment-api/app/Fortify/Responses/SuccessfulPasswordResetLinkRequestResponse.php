<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Fortify\Responses;

use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\SuccessfulPasswordResetLinkRequestResponse
    as SuccessfulPasswordResetLinkRequestResponseContract;

class SuccessfulPasswordResetLinkRequestResponse implements SuccessfulPasswordResetLinkRequestResponseContract
{
    /**
     * The response status language key.
     *
     * @var string
     */
    protected $status;

    /**
     * Create a new response instance.
     *
     * @param  string  $status
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) Ignore status and always use the same message
     * @phpstan-ignore-next-line Ignore status and always use the same message
     */
    public function __construct(string $status)
    {
        // Ignore status and always use the same message
        // So we can use the same response for both success and failure
        $this->status = PasswordBroker::RESET_LINK_SENT;
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        return $request->wantsJson()
                    ? new JsonResponse(['message' => trans($this->status)], 200)
                    : back()->with('status', trans($this->status));
    }
}
