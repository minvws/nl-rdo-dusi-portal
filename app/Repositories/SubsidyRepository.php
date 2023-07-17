<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Subsidy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class SubsidyRepository
{
    public function getSubsidy(string $id): Model|Collection|Builder|array|null
    {
        return Subsidy::query()->with('forms')->find($id);
    }
}
