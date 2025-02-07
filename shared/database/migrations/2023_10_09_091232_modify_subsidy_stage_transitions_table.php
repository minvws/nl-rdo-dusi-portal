<?php

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
        Schema::table('subsidy_stage_transitions', function (Blueprint $table)  {
            $table->boolean('assign_to_previous_assessor')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @throws Exception
     */
    public function down(): void
    {
        Schema::table('subsidy_stage_transitions', function (Blueprint $table) {
            $table->dropColumn('assign_to_previous_assessor');
        });
    }
};
