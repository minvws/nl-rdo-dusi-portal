<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationReference extends Model
{
    protected $primaryKey = 'reference';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $connection = Connection::APPLICATION;

    protected $fillable = [
        'reference',
    ];
}
