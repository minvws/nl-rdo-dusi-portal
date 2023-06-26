<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;
    use HasUuids;

    protected $connection = "pgsql_application";

    const UPDATED_AT = NULL;

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

    public function applicationHashes()
    {
        return $this->hasMany(ApplicationHash::class, 'application_id', 'id');
    }

    public function applicationReviews()
    {
        return $this->hasMany(ApplicationReview::class, 'application_id', 'id');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class, 'application_id', 'id');
    }
}
