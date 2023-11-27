<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Controllers;

use MinVWS\DUSi\Shared\User\Models\Role;
use MinVWS\DUSi\Shared\User\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use MinVWS\DUSi\Assessment\API\Http\Resources\SubsidyResource;
use MinVWS\DUSi\Shared\User\Resources\UserResource;

class UserController extends Controller
{
    public function show(Request $request): UserResource
    {
        return UserResource::make($request->user());
    }

    public function subsidies(Request $request): ResourceCollection
    {
        /** @var User $user */
        $user = $request->user();

        /**
         * @psalm-suppress InvalidTemplateParam
         */
        $subsidies = $user->roles->map(function (Role $role) {
            return $role->pivot->subsidy()->get();
        });

        $flattenedSubsidies = $subsidies->flatten();

        return SubsidyResource::collection($flattenedSubsidies);
    }
}
