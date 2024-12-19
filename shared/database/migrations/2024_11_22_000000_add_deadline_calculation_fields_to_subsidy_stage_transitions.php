<?php

declare(strict_types=1);

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
        Schema::table('subsidy_stage_transitions', static function (Blueprint $table) {
            $table->enum('target_application_review_deadline_source', [
                'field',
                'existing_deadline',
                'now',
            ])->default('existing_deadline');
            $table->jsonb('target_application_review_deadline_source_field')->nullable();
            $table->string('target_application_review_deadline_additional_period')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subsidy_stage_transitions', static function (Blueprint $table) {
            $table->dropColumn('target_application_review_deadline_source');
            $table->dropColumn('target_application_review_deadline_source_field');
            $table->dropColumn('target_application_review_deadline_additional_period');
        });
    }
};
