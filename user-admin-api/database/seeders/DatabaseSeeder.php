<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use MinVWS\DUSi\Shared\User\Models\Organisation;
use MinVWS\DUSi\Shared\User\Models\Role;
use MinVWS\DUSi\Shared\User\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
        ]);

        $organisation = Organisation::firstOrCreate([
            'name' => 'DUS-I',
        ]);

        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

//        $role = Role::firstOrCreate([
//            'name' => 'admin',
//        ]);
//
//        $organisation = Organisation::firstOrCreate([
//            'name' => 'Test Organisation',
//        ]);
//
//        $user = User::firstOrCreate(
//            ['email' => 'test@example.nl'],
//            [
//                'name' => 'Test User',
//                'password' => bcrypt('password'),
//                'organisation_id' => $organisation->id,
//            ]
//        );
//
//        if ($user->roles()->where('name', $role->name)->exists()) {
//            return;
//        }
//
//        $user->roles()->attach($role->name);
    }
}
