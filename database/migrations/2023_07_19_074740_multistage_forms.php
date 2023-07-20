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
            $table->renameColumn('form_id', 'subsidy_stage_id');
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

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
