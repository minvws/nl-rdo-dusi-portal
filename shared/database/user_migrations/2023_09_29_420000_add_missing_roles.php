<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use MinVWS\DUSi\Shared\User\Models\Connection;

return new class extends Migration
{
    protected $connection = Connection::USER;

    public function up(): void
    {
        DB::transaction(static function () {
            if (!DB::table('roles')->where('name', 'implementationCoordinator')->exists()) {
                DB::table('roles')->insert(['name' => 'implementationCoordinator', 'view_all_stages' => true]);
            }

            if (!DB::table('roles')->where('name', 'internalAuditor')->exists()) {
                DB::table('roles')->insert(['name' => 'internalAuditor', 'view_all_stages' => false]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('roles', static function () {
            DB::table('roles')->where('name', 'implementationCoordinator')->delete();
            DB::table('roles')->where('name', 'internalAuditor')->delete();
        });
    }
};
