<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldGroupPurpose extends Model
{
    public $timestamps = false;

    public function fieldGroups()
    {
        return $this->hasMany(FieldGroup::class, 'purpose', 'id');
    }

}
