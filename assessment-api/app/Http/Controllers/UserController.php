<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Controllers;

use Illuminate\Support\Collection;
use MinVWS\DUSi\Assessment\API\Http\Resources\UserResource;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\User\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use MinVWS\DUSi\Assessment\API\Http\Resources\SubsidyResource;
use MinVWS\DUSi\Shared\User\Services\UserService;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {
    }

    public function show(Request $request): UserResource
    {
        /** @var User $user */
        $user = $request->user();

        return new UserResource($user, $this->getSubsidiesForUser($user));
    }

    public function subsidies(Request $request): ResourceCollection
    {
        /** @var User $user */
        $user = $request->user();

        return SubsidyResource::collection($this->getSubsidiesForUser($user));
    }

    protected function getSubsidiesForUser(User $user): Collection
    {
        if ($this->userService->hasAccessToAllSubsidies($user)) {
            return Subsidy::all();
        }

        return $this->userService->getSubsidiesForUser($user);
    }
}
