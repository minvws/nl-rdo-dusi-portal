<?php

namespace MinVWS\DUSi\Shared\Application\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationMessage;

/**
 * @extends Factory<ApplicationMessage>
 */
class ApplicationMessageFactory extends Factory
{
    protected $model = ApplicationMessage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid,
            'application_id' => Application::factory(),
            'subject' => $this->faker->words(3, true),
            'is_new' => $this->faker->boolean,
            'sent_at' => $this->faker->dateTimeBetween('-1 year', '-1 month'),
            'seen_at' => $this->faker->dateTimeBetween('-1 month'),
            'html_path' => $this->faker->url,
            'pdf_path' => $this->faker->url,
        ];
    }
}
