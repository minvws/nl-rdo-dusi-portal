<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MinVWS\DUSi\Shared\Application\Models\Connection;
use MinVWS\DUSi\Shared\User\Enums\Role;

return new class extends Migration
{
    protected $connection = Connection::APPLICATION;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subsidy_stages', function (Blueprint $table) {
            $table->enum('assessor_user_role', array_map(fn ($role) => $role->value, Role::cases()))
                ->nullable()
                ->after('subject_role');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @throws Exception
     */
    public function down(): void
    {
        Schema::table('subsidy_stages', function (Blueprint $table) {
            $table->dropColumn('assessor_user_role');
        });
    }
};
