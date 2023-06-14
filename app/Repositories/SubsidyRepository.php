<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Subsidy;
use Illuminate\Support\Collection;

class SubsidyRepository
{
    public function getActiveSubsidies(): Collection
    {
        return Subsidy::query()->active()->ordered()->with('publishedForm')->get();
    }
}
