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
        Schema::create('application_versions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('application_id')->constrained();
            $table->timestamp('created_at')->useCurrent();
            $table->integer('version');
        });

        Schema::create('application_stages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('application_version_id')->constrained();
            $table->uuid('subsidy_stage_id');
            $table->timestamps();
            $table->uuid('user_id');
            $table->string('status');
        });

        Schema::table('answers', function (Blueprint $table) {
            $table->foreignUuid('application_stages_id')->constrained();
            $table->dropColumn('application_id');
        });

        Schema::table('application_hashes', function (Blueprint $table) {
            $table->renameColumn('form_hash_id', 'subsidy_stage_hash_id');
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->renameColumn('form_id', 'subsidy_version_id');
            $table->string('application_title');
            $table->timestamp('final_review_deadline')->nullable();
            $table->dropColumn('status');
        });

        Schema::drop('application_reviews');

        Schema::drop('judgements');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('application_versions');
        Schema::drop('application_stages');
        Schema::table('answers', function (Blueprint $table) {
            $table->dropForeign('answers_application_stages_id_foreign');
            $table->dropColumn('application_stages_id');
            $table->foreignUuid('application_id')->constrained();
        });
        Schema::table('application_hashes', function (Blueprint $table) {
            $table->renameColumn('subsidy_stage_hash_id', 'form_hash_id');
        });
        Schema::table('applications', function (Blueprint $table) {
            $table->renameColumn('subsidy_version_id', 'form_id');
            $table->dropColumn('application_title');
            $table->dropColumn('final_review_deadline');
        });
        Schema::create('application_reviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamp('created_at')->useCurrent();
            $table->foreignUuid('application_id')->constrained();
            $table->uuid('user_id');
            $table->string('judgement');
            $table->text('encrypted_comment');
            $table->string('encryption_key_id');
            $table->foreign('judgement')->references('judgement')->on('judgements');
        });
        Schema::create('judgements', function (Blueprint $table) {
            $table->string('judgement')->primary();
        });
    }
};
