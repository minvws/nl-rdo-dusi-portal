<?php

declare(strict_types=1);

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Organisation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

//        $role = Role::create([
//            'name' => 'admin',
//        ]);
//
//        $organisation = Organisation::create([
//            'name' => 'Test Organisation',
//        ]);
//
//        $user = User::create([
//            'name' => 'Test User',
//            'email' => 'test@example.nl',
//            'password' => bcrypt('password'),
//        ]);
//
//        $user->organisations()->attach($organisation->id, [
//            'role_name' => $role->name,
//        ]);
    }
}
