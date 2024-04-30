<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use MinVWS\DUSi\Shared\User\Models\Organisation;
use MinVWS\DUSi\Shared\User\Models\Role;
use MinVWS\DUSi\Shared\User\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $assessorRole = Role::where('name', 'assessor')->first();
         User::factory(10)->create([
         ])
             ->each(function ($user) use ($assessorRole) {
                 $user->roles()->attach($assessorRole);
             });
    }
}
