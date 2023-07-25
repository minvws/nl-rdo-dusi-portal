<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FieldGroupPurpose extends Model
{
    /**
     * @var string|null
     */
    protected $connection = Connection::FORM;

    public $timestamps = false;

    public function fieldGroups(): HasMany
    {
        return $this->hasMany(FieldGroup::class, 'purpose', 'id');
    }
}
