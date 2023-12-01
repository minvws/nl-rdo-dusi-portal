<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use MinVWS\DUSi\Application\API\Events\Logging\LogoutEvent;
use MinVWS\DUSi\Application\API\Models\PortalUser;

class UserController extends Controller
{
    /**
     * @throws \Exception
     */
    public function info(): JsonResponse
    {
        $user = Auth::user();
        $json = json_encode(['logged_in' => $user !== null]);
        if (is_string($json) === false) {
            throw new \Exception('Could not encode empty array to JSON');
        }
        return JsonResponse::fromJsonString($json);
    }

    public function logout(): JsonResponse
    {
        $user = Auth::user();

        assert($user instanceof PortalUser);

        $this->logger->log((new LogoutEvent())
            ->withData([
                'sessionId' => $user->getAuthIdentifier(),
                'type' => 'user',
                'typeId' => 4,
            ]));

        Auth::logout();

        return $this->info();
    }
}
