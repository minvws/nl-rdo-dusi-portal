<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MinVWS\DUSi\Shared\Application\Models\Connection;

return new class extends Migration
{
    protected $connection = Connection::APPLICATION;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('application_stages', function (Blueprint $table) {
            $table->unsignedInteger('stage');
        });

        Schema::table('application_stage_versions', function (Blueprint $table) {
            $table->enum('decision', ['pending', 'accepted', 'rejected', 'request_for_changes'])->default('pending');
            $table->uuid('assessor_user_id')->nullable();
            $table->timestamp('decision_updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('application_stages', function (Blueprint $table) {
            $table->dropColumn('stage');
        });

        Schema::table('application_stage_versions', function (Blueprint $table) {
            $table->dropColumn('decision');
            $table->dropColumn('assessor_user_id');
            $table->dropColumn('decision_updated_at');
        });
    }
};
