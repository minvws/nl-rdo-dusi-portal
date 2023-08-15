<?php

namespace MinVWS\DUSi\Shared\Subsidy\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyLetter;
use Ramsey\Uuid\Uuid;

/**
 * @extends Factory<SubsidyLetter>
 */
class SubsidyLetterFactory extends Factory
{
    protected $model = SubsidyLetter::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Uuid::uuid4(),
            'version' => $this->faker->randomDigitNotZero(),
            'status' => VersionStatus::Draft->value,
            'content_pdf' => '<p>Beste, {{ firstName }} {{ lastName }}</p>',
            'content_view' => '<p>Beste, {{ firstName }} {{ lastName }}</p>',
        ];
    }
}
