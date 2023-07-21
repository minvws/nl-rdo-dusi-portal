<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\SubsidyStage;
use App\Models\Subsidy;
use App\Services\FormService;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;

class FormController extends Controller
{
    public function __construct(
        private readonly FormService $formService,
    ) {
    }
    /**
     * Display a listing of the resource.
     */
    public function index(?Request $request): View|Factory
    {
        return view('form.index')->with([
            'forms' => $request ? SubsidyStage::query()->where('subsidy_id', $request->input('subsidy_id'))->get() : [],
            'subsidies' => Subsidy::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|Factory
    {
        return view('form.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->formService->createForm(
            $request->input('subsidy_id'),
            $request->input('version'),
            $request->input('status')
        );

        return redirect()->route('form.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(SubsidyStage $form): View|Factory
    {
        return view('form.show', [
            'form' => $form,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubsidyStage $form): View|Factory
    {
        return view('form.edit', [
            'form' => $form,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubsidyStage $form): RedirectResponse
    {
        $this->formService->updateForm(
            $form,
            $request->input('subsidy_id'),
            $request->input('version'),
            $request->input('status')
        );

        return redirect()->route('form.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubsidyStage $form): RedirectResponse
    {

        $this->formService->deleteForm($form);
        return redirect()->route('form.index');
    }
}
