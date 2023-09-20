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
        Schema::table('subsidy_stages', function (Blueprint $table) {
            $table->dropColumn('final_review_deadline');
            $table->dropColumn('final_review_time_in_s_after_submission');
        });

        Schema::table('application_stages', function (Blueprint $table) {
            $table->timestamp('submitted_at')->nullable();
            $table->boolean('is_submitted')->default(false);
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->timestamp('submitted_at')->nullable()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @throws Exception
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('submitted_at');
        });

        Schema::table('application_stages', function (Blueprint $table) {
            $table->dropColumn('is_submitted');
            $table->dropColumn('submitted_at');
        });

        Schema::table('subsidy_stages', function (Blueprint $table) {
            $table->integer('final_review_time_in_s_after_submission')->nullable();
            $table->timestamp('final_review_deadline')->nullable();
        });
    }
};
