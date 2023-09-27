<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Database\Seeders;

use Illuminate\Database\Seeder;
use MinVWS\DUSi\Shared\User\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::updateOrCreate([
            'name' => 'userAdmin', // Gebruikersbeheerder
        ], [
            'view_all_stages' => false,
        ]);

        Role::updateOrCreate([
            'name' => 'assessor', // Behandelaar
        ], [
            'view_all_stages' => false,
        ]);

        Role::updateOrCreate([
            'name' => 'implementationCoordinator', // Uitvoeringscoordinator [
        ], [
            'view_all_stages' => true,
        ]);

        Role::updateOrCreate([
            'name' => 'internalAuditor', // Interne controleur
        ], [
            'view_all_stages' => false,
        ]);
    }
}
