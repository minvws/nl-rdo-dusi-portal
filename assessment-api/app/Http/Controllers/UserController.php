<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Controllers;

use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\User\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use MinVWS\DUSi\Assessment\API\Http\Resources\SubsidyResource;
use MinVWS\DUSi\Shared\User\Resources\UserResource;
use MinVWS\DUSi\Shared\User\Services\UserService;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {
    }

    public function show(Request $request): UserResource
    {
        return UserResource::make($request->user());
    }

    public function subsidies(Request $request): ResourceCollection
    {
        /** @var User $user */
        $user = $request->user();

        if ($this->userService->hasAccessToAllSubsidies($user)) {
            return SubsidyResource::collection(Subsidy::all());
        }

        return SubsidyResource::collection($this->userService->getSubsidiesForUser($user));
    }
}
