<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\User\Enums\Role as RoleEnum;
use MinVWS\DUSi\User\Admin\API\Components\FlashNotification;
use MinVWS\DUSi\User\Admin\API\Enums\FlashNotificationTypeEnum;
use MinVWS\DUSi\User\Admin\API\Events\Logging\AddUserAuthorizationEvent;
use MinVWS\DUSi\User\Admin\API\Events\Logging\DeleteUserAuthorizationEvent;
use MinVWS\DUSi\User\Admin\API\Http\Requests\UserRoleAttachRequest;
use MinVWS\DUSi\Shared\User\Models\Role;
use MinVWS\DUSi\Shared\User\Models\User;
use MinVWS\Logging\Laravel\LogService;

class UserRolesController extends Controller
{
    public function __construct(
        private readonly LogService $logger,
    ) {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(User $user): View
    {
        return view('users.roles.index', [
            'user' => $user,
            'userRoles' => $user->roles()->paginate(),
            'roles' => Role::query()->pluck('name'),
            'subsidies' => Subsidy::query()->pluck('title', 'id'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRoleAttachRequest $request, User $user): RedirectResponse
    {
        $user->attachRole(
            role: RoleEnum::from($request->validated('role')),
            subsidyId: $request->validated('subsidy_id'),
        );

        $this->logger->log((new AddUserAuthorizationEvent())
            ->withData([
                'userId' => $user->id,
            ]));

        return redirect()
            ->route('users.roles.index', $user)
            ->with(FlashNotification::SESSION_KEY, new FlashNotification(
                type: FlashNotificationTypeEnum::CONFIRMATION,
                message: __('Role attached to user.'),
            ));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserRoleAttachRequest $request, User $user): RedirectResponse
    {
        $user->detachRole(
            role: RoleEnum::from($request->validated('role')),
            subsidyId: $request->validated('subsidy_id'),
        );

        $this->logger->log((new DeleteUserAuthorizationEvent())
            ->withData([
                'userId' => $user->id,
            ]));

        return redirect()
            ->route('users.roles.index', $user)
            ->with(FlashNotification::SESSION_KEY, new FlashNotification(
                type: FlashNotificationTypeEnum::CONFIRMATION,
                message: __('Role disconnected from user.'),
            ));
    }
}
