<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApplicationVersion extends Model
{
    use HasFactory;
    use HasUuids;

    protected $connection = Connection::Application;

    protected $fillable = [
        'version'
    ];

    const UPDATED_AT = NULL;

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_id', 'id');
    }

    public function applicationStages(): HasMany
    {
        return $this->hasMany(ApplicationStage::class, 'application_version_id', 'id');
    }


}
