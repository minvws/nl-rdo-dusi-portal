<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subsidy_stage_transitions', function (Blueprint $table) {
            $table->boolean('clone_data')->after('send_message')->default(false);
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
            $table->dropColumn('clone_data');
        });
    }
};
