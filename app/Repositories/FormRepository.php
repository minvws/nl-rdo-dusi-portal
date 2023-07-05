<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Shared\Models\Definition\Form;

class FormRepository
{
    public function getForm(string $id): ?Form
    {
        return Form::query()->open()->with('fields')->find($id);
    }
}
