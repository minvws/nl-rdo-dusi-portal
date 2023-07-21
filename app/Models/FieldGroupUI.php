<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldGroupUI extends Model
{
    use HasFactory;
    use HasUuids;

    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'default_input_ui' => 'json',
        'default_review_ui' => 'json',
    ];

    protected $fillable = [
        'field_group_id',
        'version',
        'status',
        'default_input_ui',
        'default_review_ui',
        'created_at',
        'updated_at'
    ];

    public function fieldGroup()
    {
        return $this->belongsTo(FieldGroup::class, 'field_group_id', 'id');
    }
}
