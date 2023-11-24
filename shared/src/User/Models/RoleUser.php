<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\User\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;

/**
 * @property string $subsidy_id
 */
class RoleUser extends Pivot
{
    public function subsidy()
    {
        return $this->belongsTo(Subsidy::class, 'subsidy_id', 'id');
    }
}
