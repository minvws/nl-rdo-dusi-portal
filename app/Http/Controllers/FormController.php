<?php

namespace App\Http\Controllers;

use App\Models\Enums\VersionStatus;
use App\Models\Form;
use App\Models\Field;
use App\Models\Subsidy;
use Illuminate\Http\Request;
use Ramsey\Uuid\Rfc4122\UuidV4;
class FormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('form.index')->with('forms', Form::all());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('form.create')->with('subsidies', Subsidy::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        Form::create([
            'id' => UuidV4::uuid4(),
            'subsidy_id' => $request->subsidy_id,
            'status' => 'draft',
            'version' => 1,
        ]);
        return $this->index();
    }

    /**
     * Display the specified resource.
     */
    public function show(Form $form)
    {
        return view('form.show')->with('form', $form);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Form $form)
    {
        return view('form.edit')->with('form', $form)->with('statusOptions', VersionStatus::cases())->with('subsidies', Subsidy::all());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Form $form)
    {
        //
        Form::where('id', $form->id)->update([
            'subsidy_id' => $request->subsidy_id,
            'status' => $request->status,
            'version' => $request->version,
        ]);

        return $this->index();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Form $form)
    {
        Field::destroy($form->fields->pluck('id'));
        Form::destroy($form->id);
        return $this->index();
    }
}
