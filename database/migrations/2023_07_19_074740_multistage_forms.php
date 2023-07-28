<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;

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

        Schema::table('subsidies', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::create('subsidy_versions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamp('created_at')->useCurrent();
            $table->foreignUuid('subsidy_id')->constrained();
            $table->unsignedTinyInteger('version');
            $table->string('status');
        });

        Schema::table('subsidy_stages', function (Blueprint $table) {
            $table->dropColumn('updated_at');
            $table->dropColumn('version');
            $table->dropColumn('status');
            $table->dropColumn('subsidy_id');
            $table->foreignUuid('subsidy_version_id')->constrained();
            $table->string('title');
            $table->string('subject_role');
            $table->string('subject_organisation')->nullable();
            $table->integer('stage');
            $table->timestamp('final_review_deadline')->nullable();
            $table->integer('final_review_time_in_s_after_submission')->nullable();
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
            $table->dropColumn('form_id');
        });

        Schema::table('subsidy_stage_uis', function (Blueprint $table) {
            $table->renameColumn('form_id', 'subsidy_stage_id');
        });


        Schema::create('field_group_purposes', function (Blueprint $table) {
            $table->string('id')->primary();
        });

        schema::create('field_groups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('field_id')->constrained();
            $table->string('purpose');
            $table->foreign('purpose')->references('id')->on('field_group_purposes');
            $table->unsignedTinyInteger('version');
            $table->string('status');
            $table->string('title');
            $table->timestamps();
        });

        Schema::create('field_group_uis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('field_group_id')->constrained();
            $table->unsignedTinyInteger('version');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->json('default_input_ui');
            $table->json('default_review_ui');
            $table->timestamps();
        });

        Schema::create('field_subsidy_stage', function (Blueprint $table){
            $table->primary(['field_id', 'subsidy_stage_id']);
            $table->foreignUuid('field_id')->constrained();
            $table->foreignUuid('subsidy_stage_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        // Drop new tables
        Schema::dropIfExists('field_group_uis');
        Schema::dropIfExists('field_groups');
        Schema::dropIfExists('field_group_purposes');
        Schema::dropIfExists('field_subsidy_stage');

        // Restoring the 'subsidy_stage_hashes' table
        Schema::table('subsidy_stage_hashes', function (Blueprint $table) {
            $table->dropTimestamps();
            $table->renameColumn('description', 'short_description');
            $table->renameColumn('subsidy_stage_id', 'form_id');
            $table->dropColumn('name');
        });

        // Restoring the 'subsidy_stage_hash_fields' table
        Schema::table('subsidy_stage_hash_fields', function (Blueprint $table) {
            $table->renameColumn('subsidy_stage_hash_id', 'form_hash_id');
        });

        Field::query()->truncate();

        // Restoring the 'fields' table
        Schema::table('fields', function (Blueprint $table) {
            $table->foreignUuid('form_id')->references('id')->on('subsidy_stages');
        });

        // Restoring the 'subsidy_stage_uis' table
        Schema::table('subsidy_stage_uis', function (Blueprint $table) {
            $table->renameColumn('subsidy_stage_id', 'form_id');
        });

        SubsidyStage::query()->truncate();
        // Restoring the previous state of the tables
        Schema::table('subsidy_stages', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('subject_role');
            $table->dropColumn('subject_organisation');
            $table->dropColumn('stage');
            $table->dropColumn('final_review_deadline');
            $table->dropColumn('final_review_time_in_s_after_submission');
            $table->dropForeign(['subsidy_version_id']);
            $table->dropColumn('subsidy_version_id');
            $table->unsignedTinyInteger('version');
            $table->string('status');
            $table->uuid('subsidy_id')->constrained('subsidies')->restrictOnDelete();
            $table->timestamp('updated_at')->useCurrent();
        });

        // Dropping the 'subsidy_versions' table
        Schema::dropIfExists('subsidy_versions');

        Schema::table('subsidies', function (Blueprint $table) {
            $table->dropTimestamps();
        });
        // Reverting the table renames from 'up()' method
        Schema::rename('subsidy_stage_uis', 'form_uis');
        Schema::rename('subsidy_stage_hash_fields', 'form_hash_fields');
        Schema::rename('subsidy_stage_hashes', 'form_hashes');
        Schema::rename('subsidy_stages', 'forms');
    }
};
