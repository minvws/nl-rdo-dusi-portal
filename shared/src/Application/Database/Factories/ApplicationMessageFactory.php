<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationMessage;
use MinVWS\DUSi\Shared\Application\Models\Disk;
use MinVWS\DUSi\Shared\Application\Models\Identity;

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

    public function forIdentity(Identity $identity): self
    {
        return $this->state(fn () => [
            'application_id' => Application::factory()->forIdentity($identity),
        ]);
    }

    public function withLetter(string $body = 'DUMMY'): self
    {
        $stageSequenceNumber = $this->faker->unique()->numberBetween();

        $state = $this->state(fn (array $attributes) => [
            'html_path' => sprintf(
                'applications/%s/letters/%d/%s.html',
                $attributes['id'],
                $stageSequenceNumber,
                'letter'
            ),
            'pdf_path' => sprintf(
                'applications/%s/letters/%d/%s.pdf',
                $attributes['id'],
                $stageSequenceNumber,
                'letter'
            ),
        ]);

        return $state->afterMaking(function (ApplicationMessage $applicationMessage) use ($body) {
            $filesystem = Storage::disk(Disk::APPLICATION_FILES);

            $filesystem->put($applicationMessage->html_path, sprintf('%s HTML', $body));
            $filesystem->put($applicationMessage->pdf_path, sprintf('%s PDF', $body));
        });
    }
}
