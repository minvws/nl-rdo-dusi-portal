<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\User\Admin\API\Components\FlashNotification;
use MinVWS\DUSi\User\Admin\API\Enums\FlashNotificationTypeEnum;
use MinVWS\DUSi\User\Admin\API\Http\Requests\UserRoleAttachRequest;
use MinVWS\DUSi\User\Admin\API\Models\Role;
use MinVWS\DUSi\User\Admin\API\Models\User;

class UserRolesController extends Controller
{
    public function __construct()
    {
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
            role: $request->validated('role'),
            subsidyId: $request->validated('subsidy_id'),
        );

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
            role: $request->validated('role'),
            subsidyId: $request->validated('subsidy_id'),
        );

        return redirect()
            ->route('users.roles.index', $user)
            ->with(FlashNotification::SESSION_KEY, new FlashNotification(
                type: FlashNotificationTypeEnum::CONFIRMATION,
                message: __('Role disconnected from user.'),
            ));
    }
}
