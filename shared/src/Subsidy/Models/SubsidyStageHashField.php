<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MinVWS\DUSi\Shared\Subsidy\Database\Factories\SubsidyStageHashFieldFactory;
use MinVWS\DUSi\Shared\Subsidy\Traits\HasCompositePrimaryKey;

class SubsidyStageHashField extends Model
{
    use HasCompositePrimaryKey;
    use HasFactory;

    protected $connection = Connection::APPLICATION;

    public $timestamps = false;

    /*
     * @var array
     */
    /** @phpstan-ignore-next-line */
    protected $primaryKey = ['subsidy_stage_hash_id', 'field_id'];

    public function subsidyStageHash(): BelongsTo
    {
        return $this->belongsTo(SubsidyStageHash::class, 'subsidy_stage_hash_id', 'id');
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class, 'field_id', 'id');
    }

    protected static function newFactory(): SubsidyStageHashFieldFactory
    {
        return new SubsidyStageHashFieldFactory();
    }
}
