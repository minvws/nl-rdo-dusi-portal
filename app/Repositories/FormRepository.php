<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Field;
use App\Models\SubsidyStage;
use App\Models\FormUI;
use App\Models\Subsidy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class FormRepository
{
    public function getSubsidy(string $id): Model|Collection|Builder|array|null
    {
        return Subsidy::query()->find($id);
    }

    public function getForm(string $id): Model|Collection|Builder|array|null
    {
        return SubsidyStage::query()->find($id);
    }

    public function getField(string $id): Model|Collection|Builder|array|null
    {
        return Field::query()->find($id);
    }
    public function makeSubsidy(): Subsidy
    {
        return new Subsidy();
    }

    public function saveSubsidy(Subsidy $subsidy): void
    {
        $subsidy->save();
    }

    public function makeForm(Subsidy $subsidy): SubsidyStage
    {
        $form = new SubsidyStage();
        $form->subsidy()->associate($subsidy);
        return $form;
    }

    public function saveForm(SubsidyStage $form): void
    {
        $form->save();
    }

    public function makeFormUI(SubsidyStage $form): FormUI
    {
        $formUI = new FormUI();
        $formUI->form()->associate($form);
        return $formUI;
    }

    public function saveFormUI(FormUI $formUI): void
    {
        $formUI->save();
    }

    public function makeField(SubsidyStage $form): Field
    {
        $field = new Field();
        $field->form()->associate($form);
        return $field;
    }

    public function saveField(Field $field): void
    {
        $field->save();
    }
}
