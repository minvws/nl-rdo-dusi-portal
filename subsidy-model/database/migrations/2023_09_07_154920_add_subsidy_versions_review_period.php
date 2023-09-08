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
        Schema::table('subsidy_versions', function (Blueprint $table) {
            $table->unsignedMediumInteger('review_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subsidy_versions', function (Blueprint $table) {
            $table->dropColumn('review_period');
        });
    }
};
