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


    public function applicationVersion(): BelongsTo
    {
        return $this->belongsTo(ApplicationVersion::class, 'application_version_id', 'id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class, 'application_stages_id', 'id');
    }

}
