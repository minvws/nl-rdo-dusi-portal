<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Shared\Models\Definition\Form;
use App\Shared\Models\Definition\Subsidy;
use Illuminate\Support\Collection;

class FormRepository
{
    public function getOpenFormsForSubsidy(Subsidy $subsidy): Collection
    {
        return $subsidy->forms()->open()->ordered()->get();
    }

    public function getForm(string $id): ?Form
    {
        return Form::query()->open()->with('fields')->find($id);
    }
}
