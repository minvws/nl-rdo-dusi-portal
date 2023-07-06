<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasCompositePrimaryKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationHash extends Model
{
    use HasCompositePrimaryKey;
    use HasFactory;

    protected $connection = "pgsql_application";

    /**
     * Get the primary key for the model.
     *
     * @return array|string
     */
    public function getKeyName()
    {
        return ['form_hash_id', 'application_id'];
    }

    /**
     * @var false
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'form_hash_id',
        'application_id',
        'hash'
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_id', 'id');
    }
}
