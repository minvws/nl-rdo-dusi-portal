<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationReference extends Model
{
    public const UPDATED_AT = null;

    public $incrementing = false;

    protected $primaryKey = 'reference';
    protected $keyType = 'string';

    protected $connection = Connection::APPLICATION;

    protected $fillable = [
        'reference',
    ];
}
