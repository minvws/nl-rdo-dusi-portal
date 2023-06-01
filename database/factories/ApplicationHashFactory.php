<?php

namespace Database\Factories;

use App\Models\Application;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicationHashFactory extends Factory
{
    public function definition():array
    {
        return [
            'application_id' => Application::factory(),
            'form_hash_id' => $this->faker->uuid,
            'hash' => $this->faker->text,
        ];
    }
}
