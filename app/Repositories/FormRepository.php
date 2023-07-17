<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Shared\Models\Definition\Field;
use App\Shared\Models\Definition\Form;
use Illuminate\Support\Collection;

class FormRepository
{
    public function getForm(string $id): ?Form
    {
        $form = Form::query()->open()->find($id);
        assert($form === null || $form instanceof Form);
        return $form;
    }

    public function getField(Form $form, string $fieldCode): ?Field
    {
        $field = $form->fields()->where('code', '=', $fieldCode)->first();
        assert($field === null || $field instanceof Field);
        return $field;
    }

    public function getFields(Form $form): Collection
    {
        return $form->fields;
    }
}
