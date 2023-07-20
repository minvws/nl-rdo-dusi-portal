<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\Pivot;

class OrganisationRole extends Pivot
{
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'organisation_id',
        'role_id',
        'user_id',
        'role_name',
    ];
}
