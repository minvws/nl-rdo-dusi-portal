<?php

namespace App\Models;

use App\Shared\Models\Application\Identity;
use App\Shared\Models\Application\IdentityType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    use HasFactory;
    use HasUuids;

    protected $connection = Connection::Application;

    protected $casts = [
        'identity_type' => IdentityType::class,
        'locked_from' => 'timestamp',
        'final_review_deadline' => 'timestamp',
    ];

    protected $fillable = [
        'subsidy_version_id',
        'application_title',
        'final_review_deadline',
        'locked_from'
    ];

    public function applicationHashes(): HasMany
    {
        return $this->hasMany(ApplicationHash::class, 'application_id', 'id');
    }

    public function applicationStages(): HasMany
    {
        return $this->hasMany(ApplicationStage::class, 'application_id', 'id');
    }

    protected function identity(): Attribute
    {
        return Attribute::make(
            get: fn (array $attributes) => new Identity(
                IdentityType::from($attributes['identity_type']),
                $attributes['identity_identifier']
            ),
            set: fn (Identity $identity) => [
                'identity_type' => $identity->type,
                'identity_identifier' => $identity->identifier
            ]
        );
    }
}
