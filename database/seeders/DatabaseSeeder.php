<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Application;
use App\Models\ApplicationHash;
use App\Models\ApplicationReview;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Application::factory(100)
            ->has(ApplicationReview::factory(10))
            ->has(ApplicationHash::factory(10))
            ->has(Answer::factory(18))
            ->create();
    }
}
