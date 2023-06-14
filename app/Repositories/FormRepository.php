<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Form;
use App\Models\Subsidy;
use Illuminate\Support\Collection;

class FormRepository
{
    public function getOpenFormsForSubsidy(Subsidy $subsidy): Collection
    {
        return $subsidy->forms()->open()->ordered()->get();
    }

    public function getForm(string $id): ?Form
    {
        return Form::query()->open()->find($id);
    }
}
