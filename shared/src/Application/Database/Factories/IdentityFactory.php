<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\IdentityType;

/**
 * @extends Factory<Identity>
 */
class IdentityFactory extends Factory
{
    protected $model = Identity::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $identifier = (string)$this->faker->randomNumber(9);
        $createdAt = $this->faker->dateTimeBetween('-7 days');

        return [
            'id' => $this->faker->uuid,
            'type' => IdentityType::CitizenServiceNumber,
            'encrypted_identifier' => base64_encode($identifier), // can't encrypt in the factory
            'hashed_identifier' => base64_encode(hash('sha256', $identifier, true)),
            'created_at' => $createdAt,
            'updated_at' => $createdAt
        ];
    }
}
