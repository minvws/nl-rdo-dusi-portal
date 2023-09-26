<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\User\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 * @property bool $view_all_stages
 */
class Role extends Model
{
    protected $connection = Connection::USER;
    protected $primaryKey = 'name';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'view_all_stages',
    ];
}
