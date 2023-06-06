<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Judgement;
use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationController extends BaseController
{
    public function __construct()
    {
    }

    /**
     * @return Application|Factory|View
     */
    public function overview(Request $request)
    {
        $applications = Application::query()
            ->where('judgement', $request->get('judgement', 'pending'))
            ->paginate((int)$request->get('pageLength', 50));

        return view('applications')
            ->with('applications', $applications)
            ->with('judgements', Judgement::all()->pluck('judgement'));
    }
}
