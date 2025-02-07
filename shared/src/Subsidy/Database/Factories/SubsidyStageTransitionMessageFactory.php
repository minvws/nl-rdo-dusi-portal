<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransition;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStageTransitionMessage;
use Ramsey\Uuid\Uuid;

/**
 * @extends Factory<SubsidyStageTransitionMessage>
 */
class SubsidyStageTransitionMessageFactory extends Factory
{
    protected $model = SubsidyStageTransitionMessage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Uuid::uuid4(),
            'subsidy_stage_transition_id' => SubsidyStageTransition::factory(),
            'version' => $this->faker->randomDigitNotZero(),
            'status' => VersionStatus::Published,
            'subject' => $this->faker->words(3, true),
            'content_pdf' => '<p>Beste, {{ firstName }} {{ lastName }}</p>',
            'content_html' => '<p>Beste, {{ firstName }} {{ lastName }}</p>',
        ];
    }
}
