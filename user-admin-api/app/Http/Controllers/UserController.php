<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use MinVWS\DUSi\User\Admin\API\Components\FlashNotification;
use MinVWS\DUSi\User\Admin\API\Enums\FlashNotificationTypeEnum;
use MinVWS\DUSi\User\Admin\API\Events\Logging\CreateUserEvent;
use MinVWS\DUSi\User\Admin\API\Events\Logging\UpdateUserEvent;
use MinVWS\DUSi\User\Admin\API\Events\Logging\ViewUserEvent;
use MinVWS\DUSi\User\Admin\API\Http\Requests\UserCreateRequest;
use MinVWS\DUSi\User\Admin\API\Http\Requests\UserFilterRequest;
use MinVWS\DUSi\User\Admin\API\Http\Requests\UserResetCredentialsRequest;
use MinVWS\DUSi\User\Admin\API\Http\Requests\UserUpdateActiveRequest;
use MinVWS\DUSi\User\Admin\API\Http\Requests\UserUpdateRequest;
use MinVWS\DUSi\Shared\User\Models\Organisation;
use MinVWS\DUSi\Shared\User\Models\User;
use MinVWS\DUSi\User\Admin\API\Services\UserService;
use MinVWS\DUSi\User\Admin\API\View\Data\UserCredentialsData;
use MinVWS\Logging\Laravel\LogService;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UserController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected readonly LogService $logger,
    ) {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(UserFilterRequest $filterRequest): View
    {
        $users = User::query()
            ->with('organisation')
            ->filterByNameOrEmail($filterRequest->validated('filter'))
            ->paginate();

        return view('users.index', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('users.create')
            ->with([
                'organisations' => Organisation::query()->pluck('name', 'id')
            ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserCreateRequest $request): RedirectResponse
    {
        $password = $this->userService->generatePassword();

        $user = $this->userService->createUser(
            name: $request->validated('name'),
            email: $request->validated('email'),
            password: $password,
            organisationId: $request->validated('organisation_id'),
        );

        $this->logger->log((new CreateUserEvent())
            ->withActor($request->user())
            ->withData([
                'userId' => $user->id,
                'type' => 'user',
                'typeId' => 4,
            ]));

        return redirect()
            ->route('users.credentials', $user->id)
            ->with(UserCredentialsData::SESSION_KEY, new UserCredentialsData(
                user: $user,
                password: $password,
                twoFactorAuthenticationReset: true,
            ));
    }

    public function credentials(User $user): View|RedirectResponse
    {
        $this->authorize('resetCredentials', $user);

        $userCredentialsData = Session::get(UserCredentialsData::SESSION_KEY);
        if (!($userCredentialsData instanceof UserCredentialsData) || $userCredentialsData->user->id !== $user->id) {
            return redirect()
                ->route('users.index');
        }

        return view('users.credentials', [
            'user' => $userCredentialsData->user,
            'password' => $userCredentialsData->password,
            'twoFactorAuthenticationReset' => $userCredentialsData->twoFactorAuthenticationReset,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, User $user): View
    {
        $this->logger->log((new ViewUserEvent())
           ->withActor($request->user())
           ->withData([
                'userId' => $user->id,
                'type' => 'user',
                'typeId' => 4,
            ]));

        return view('users.show', [
            'user' => $user,
            'organisations' => Organisation::query()->pluck('name', 'id')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        $user->update($request->validated());

        $this->logger->log((new UpdateUserEvent())
            ->withActor($request->user())
            ->withData([
                'userId' => $user->id,
                'type' => 'user',
                'typeId' => 4,
            ]));

        return redirect()
            ->route('users.show', $user->id)
            ->with(FlashNotification::SESSION_KEY, new FlashNotification(
                type: FlashNotificationTypeEnum::CONFIRMATION,
                message: __('User updated.'),
            ));
    }

    public function updateActive(UserUpdateActiveRequest $updateActiveRequest, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $user->update($updateActiveRequest->validated());

        $this->logger->log((new UpdateUserEvent())
            ->withActor($updateActiveRequest->user())
            ->withData([
                'userId' => $user->id,
                'type' => 'user',
                'typeId' => 4,
            ]));

        return redirect()
            ->route('users.show', $user->id)
            ->with(FlashNotification::SESSION_KEY, new FlashNotification(
                type: FlashNotificationTypeEnum::CONFIRMATION,
                message: __('User updated.'),
            ));
    }

    public function resetCredentials(UserResetCredentialsRequest $resetCredentialsRequest, User $user): RedirectResponse
    {
        $this->authorize('resetCredentials', $user);

        if (empty($resetCredentialsRequest->validated())) {
            return redirect()
                ->route('users.show', $user->id);
        }

        $credentialsData = $this->userService->resetUserPassword(
            user: $user,
            resetPassword: (bool) $resetCredentialsRequest->validated('reset_password'),
            resetTwoFactor: (bool) $resetCredentialsRequest->validated('reset_2fa'),
        );

        return redirect()
            ->route('users.credentials', $user->id)
            ->with(UserCredentialsData::SESSION_KEY, $credentialsData);
    }
}
