<?php

namespace App\Models;

use App\Shared\Models\Application\Identity;
use App\Shared\Models\Application\IdentityType;
use App\Shared\Models\Definition\Form;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $form_id
 * @property Form $form
 * @property Identity $identity
 * @property ApplicationStatus $status
 */
class Application extends Model
{
    use HasFactory;
    use HasUuids;

    protected $connection = Connection::Application;

    protected $casts = [
        'identity_type' => IdentityType::class,
        'locked_from' => 'timestamp',
        'status' => ApplicationStatus::class
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

    protected function identity(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => new Identity(
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
