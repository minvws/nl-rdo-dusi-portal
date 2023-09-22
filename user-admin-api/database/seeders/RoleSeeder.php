<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Database\Seeders;

use Illuminate\Database\Seeder;
use MinVWS\DUSi\User\Admin\API\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate([
            'name' => 'admin',
        ]);
        Role::firstOrCreate([
            'name' => 'practitioner',
        ]);
        Role::firstOrCreate([
            'name' => 'implementationCoordinator',
        ]);
        Role::firstOrCreate([
            'name' => 'internalAuditor',
        ]);
    }
}
