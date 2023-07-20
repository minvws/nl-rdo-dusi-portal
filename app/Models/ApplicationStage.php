<?php

namespace App\Models;

use App\Models\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApplicationStage extends Model
{
    use HasFactory;
    use HasUuids;

    protected $connection = Connection::Application;

    protected $casts = [
        'status' => ApplicationStatus::class,
    ];
    protected $fillable = [
        'subsidy_stage_id',
        'user_id',
        'status',
    ];


    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_id', 'id');
    }

    public function applicationStageVersions(): HasMany
    {
        return $this->hasMany(ApplicationStageVersion::class, 'application_stages_id', 'id');
    }
}
