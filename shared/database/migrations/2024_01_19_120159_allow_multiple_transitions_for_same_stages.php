<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MinVWS\DUSi\Shared\Application\Models\Connection;

return new class extends Migration
{
    protected $connection = Connection::APPLICATION;

    public function up(): void
    {
        Schema::table('subsidy_stage_transitions', function (Blueprint $table) {
            $table->dropUnique(['current_subsidy_stage_id', 'target_subsidy_stage_id']);
        });
    }

    public function down(): void
    {
        Schema::table('subsidy_stage_transitions', function (Blueprint $table) {
            $table->unique(['current_subsidy_stage_id', 'target_subsidy_stage_id']);
        });
    }
};
