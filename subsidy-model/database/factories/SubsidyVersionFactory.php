<?php

namespace MinVWS\DUSi\Shared\Subsidy\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use Ramsey\Uuid\Uuid;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;

/**
 * @extends Factory<SubsidyVersion>
 */
class SubsidyVersionFactory extends Factory
{
    protected $model = SubsidyVersion::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Uuid::uuid4(),
            'subsidy_id' => Subsidy::factory(),
            'version' => $this->faker->randomDigitNotZero(),
            'status' => VersionStatus::Draft->value,
            'subsidy_page_url' => $this->faker->url(),
            'contact_mail_address' => 'dienstpostbus@minvws.nl',
            'mail_to_address_field_identifier' => 'email',
            'mail_to_name_field_identifier' => 'firstName;infix;lastName',
            'message_overview_subject' => $this->faker->words(3, true),
            'review_period' => rand(1, 20) * 7
        ];
    }
}
