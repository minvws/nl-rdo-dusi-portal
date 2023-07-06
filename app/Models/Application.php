<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    use HasFactory;
    use HasUuids;

    protected $connection = "pgsql_application";

    public const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'form_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'locked_from' => 'timestamp',
    ];

    public function applicationHashes(): HasMany
    {
        return $this->hasMany(ApplicationHash::class, 'application_id', 'id');
    }

    public function applicationReviews(): HasMany
    {
        return $this->hasMany(ApplicationReview::class, 'application_id', 'id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class, 'application_id', 'id');
    }
}
