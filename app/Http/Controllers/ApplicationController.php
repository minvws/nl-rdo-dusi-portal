<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UpdateApplicationRequest;
use App\Models\Judgement;
use App\Models\Application;
use App\Models\ApplicationReview;
use Ramsey\Uuid\Rfc4122\UuidV4;

class ApplicationController extends BaseController
{
    public function __construct()
    {
    }

    /**
     * Index applications
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $applications = Application::query();

        if ($request->has('judgement')) {
            $applications->where('judgement', $request->validate([
                'judgement' => 'required|in:pending,rejected,approved',
            ])['judgement']);
        } else {
            $applications->where('judgement', 'pending');
        }

        $applications = $applications->get();

        return view('applications.index')
            ->with('applications', $applications)
            ->with('judgements', Judgement::all()->pluck('judgement'));
    }

    /**
     * show application
     *
     * @param Application $application
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function show(Application $application)
    {
        return view('applications.show')
            ->with('application', $application)
            ->with('answers', $application->answers);
    }

    /**
     * Update application
     *
     * @param Application $application
     * @param UpdateApplicationRequest $request
     *
     * @return \Illuminate\Routing\Redirector | \Illuminate\Http\RedirectResponse
     */
    public function update(Application $application, UpdateApplicationRequest $request)
    {
        $judgement = $request->validated('judgement-select');
        $reasons = $request->validated('reason');

        if ($judgement == 'rejected') {
            ApplicationReview::create([
                'application_id' => $application->id,
                'encrypted_comment' => $reasons,
                'user_id' => UuidV4::uuid4(),
                'judgement' => $judgement,
                'encryption_key_id' => UuidV4::uuid4(),
            ]);
        }

        $application->judgement = $judgement;
        $application->save();

        return redirect()->route('applications.show', $application);
    }
}
