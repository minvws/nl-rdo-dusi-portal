<?php

use App\Models\ApplicationStatus;
use App\Shared\Models\Application\IdentityType;
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
        Schema::rename('forms', 'subsidy_stages');
        Schema::rename('form_hashes', 'subsidy_stage_hashes');
        Schema::rename('form_hash_fields', 'subsidy_stage_hash_fields');
        Schema::rename('form_uis', 'subsidy_stage_uis');

        Schema::table('subsidy_stages', function (Blueprint $table) {
            $table->dropColumn('updated_at');
            $table->dropColumn('version');
            $table->dropColumn('status');
            $table->string('title');
            $table->string('subject_role');
            $table->string('subject_organisation');
            $table->integer('stage');
            $table->timestamp('final_review_deadline')->nullable();
            $table->integer('final_review_in_s_after_submission')->nullable();
        });

        Schema::table('subsidy_stage_hashes', function (Blueprint $table) {
            $table->timestamps();
            $table->renameColumn('short_description', 'description');
            $table->renameColumn('form_id', 'subsidy_stage_id');
            $table->string('name');
        });

        Schema::table('subsidy_stage_hash_fields', function (Blueprint $table) {
            $table->renameColumn('form_hash_id', 'subsidy_stage_hash_id');
        });

        Schema::table('fields', function (Blueprint $table) {
            $table->renameColumn('form_id', 'subsidy_stage_id');
            $table->string('code', 100);
        });

        Schema::table('subsidy_stage_uis', function (Blueprint $table) {
            $table->renameColumn('form_id', 'subsidy_stage_id');
        });

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
