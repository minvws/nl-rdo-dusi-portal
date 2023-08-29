<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use MinVWS\DUSi\User\Admin\API\Components\FlashNotification;
use MinVWS\DUSi\User\Admin\API\Enums\FlashNotificationTypeEnum;
use MinVWS\DUSi\User\Admin\API\Http\Requests\OrganisationCreateRequest;
use MinVWS\DUSi\User\Admin\API\Http\Requests\OrganisationUpdateRequest;
use MinVWS\DUSi\User\Admin\API\Models\Organisation;

class OrganisationController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(Organisation::class, 'organisation');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $organisations = Organisation::query()->paginate();

        return view('organisations.index', [
            'organisations' => $organisations,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('organisations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrganisationCreateRequest $request): RedirectResponse
    {
        Organisation::create($request->validated());

        return redirect()
            ->route('organisations.index')
            ->with(FlashNotification::SESSION_KEY, new FlashNotification(
                type: FlashNotificationTypeEnum::CONFIRMATION,
                message: __('Organisation created.'),
            ));
    }

    /**
     * Display the specified resource.
     */
    public function show(Organisation $organisation): View
    {
        return view('organisations.show', [
            'organisation' => $organisation,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrganisationUpdateRequest $request, Organisation $organisation): RedirectResponse
    {
        $organisation->update($request->validated());

        return redirect()
            ->route('organisations.index')
            ->with(FlashNotification::SESSION_KEY, new FlashNotification(
                type: FlashNotificationTypeEnum::CONFIRMATION,
                message: __('Organisation updated.'),
            ));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organisation $organisation): RedirectResponse
    {
        $organisation->delete();

        return redirect()
            ->route('organisations.index')
            ->with(FlashNotification::SESSION_KEY, new FlashNotification(
                type: FlashNotificationTypeEnum::CONFIRMATION,
                message: __('Organisation deleted.'),
            ));
    }
}
