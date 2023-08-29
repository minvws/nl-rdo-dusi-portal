<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use MinVWS\DUSi\User\Admin\API\Components\FlashNotification;
use MinVWS\DUSi\User\Admin\API\Enums\FlashNotificationTypeEnum;
use MinVWS\DUSi\User\Admin\API\Http\Requests\UserOrganisationAttachRequest;
use MinVWS\DUSi\User\Admin\API\Models\Organisation;
use MinVWS\DUSi\User\Admin\API\Models\Role;
use MinVWS\DUSi\User\Admin\API\Models\User;

class UserOrganisationsController extends Controller
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
        return view('users.organisations.index', [
            'user' => $user,
            'organisations' => $user->organisations()->paginate(),
            'allOrganisations' => Organisation::query()->pluck('name', 'id'),
            'roles' => Role::query()->pluck('name'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserOrganisationAttachRequest $request, User $user): RedirectResponse
    {
        $user->organisations()->attach(
            $request->validated('organisation_id'),
            ['role_name' => $request->validated('role')],
        );

        return redirect()
            ->route('users.organisations.index', $user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user, Organisation $organisation): RedirectResponse
    {
        $user->organisations()->detach($organisation);

        return redirect()
            ->route('users.organisations.index', $user)
            ->with(FlashNotification::SESSION_KEY, new FlashNotification(
                type: FlashNotificationTypeEnum::CONFIRMATION,
                message: __('Organisation disconnected from user.'),
            ));
    }
}
