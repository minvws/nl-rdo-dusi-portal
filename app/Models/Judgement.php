<?php

declare(strict_types=1);

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

    protected $connection = "pgsql_application";

    protected $primaryKey = 'judgement';

    /**
     * @var false
     */
    public $incrementing = false;

    /**
     * @var string[]
     */
    protected $fillable = [
        'judgement',
    ];

    /**
     * @var array<string, string> casts
     *
     */
    protected $casts = [
        'judgement' => Enums\Judgement::class
    ];
}
