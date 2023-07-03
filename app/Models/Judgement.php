<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Judgement
 *
 * @property string $judgement
 * @mixin Builder
 */
class Judgement extends Model
{
    use HasFactory;

    protected $connection = Connection::Application;

    protected $primaryKey = 'judgement';

    public $incrementing = false;

    protected $fillable = [
        'judgement',
    ];

    protected $casts = [
        'judgement' => Enums\Judgement::class
    ];
}
