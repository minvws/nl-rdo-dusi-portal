<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MinVWS\DUSi\Shared\Application\Database\Factories\ApplicationHashFactory;
use MinVWS\DUSi\Shared\Application\Traits\HasCompositePrimaryKey;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageHash;

class ApplicationHash extends Model
{
    use HasFactory;
    use HasCompositePrimaryKey;

    protected $connection = Connection::APPLICATION;

    protected $primaryKey = ['subsidy_stage_hash_id', 'application_id']; // @phpstan-ignore-line

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'subsidy_stage_hash_id',
        'application_id',
        'hash'
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_id', 'id');
    }

    public function subsidyStageHash(): BelongsTo
    {
        return $this->belongsTo(SubsidyStageHash::class, 'subsidy_stage_hash_id', 'id');
    }

    protected static function newFactory(): ApplicationHashFactory
    {
        return ApplicationHashFactory::new();
    }
}
