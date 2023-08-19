<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MinVWS\DUSi\Shared\Application\Models\Answer;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationHash;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStageVersion;
use MinVWS\DUSi\Shared\Application\Models\Connection;
use MinVWS\DUSi\Shared\Application\Models\Enums\ApplicationStageVersionStatus;


return new class extends Migration
{
    protected $connection = Connection::APPLICATION;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::drop('application_reviews');

        Schema::drop('judgements');

        Schema::table('applications', function (Blueprint $table) {
            $table->renameColumn('form_id', 'subsidy_version_id');
            $table->string('application_title');
            $table->timestamp('final_review_deadline')->nullable();
            $table->dropColumn('status');
        });

        Schema::table('application_hashes', function (Blueprint $table) {
            $table->renameColumn('form_hash_id', 'subsidy_stage_hash_id');
        });

        Schema::create('application_stages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('application_id')->constrained();
            $table->uuid('subsidy_stage_id');
            $table->timestamps();
            $table->uuid('user_id')->nullable();
        });

        Schema::create('application_stage_versions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('application_stage_id')->constrained();
            $table->timestamp('created_at')->useCurrent();
            $table->string('status');
            $table->integer('version');
        });

        Schema::table('answers', function (Blueprint $table) {
            $table->foreignUuid('application_stage_version_id')->constrained();
            $table->dropColumn('application_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Answer::query()->truncate();
        ApplicationStageVersion::query()->truncate();
        ApplicationStage::query()->truncate();
        ApplicationHash::query()->truncate();
        Application::query()->truncate();

        Schema::table('answers', function (Blueprint $table) {
            $table->dropForeign('answers_application_stage_version_id_foreign');
            $table->dropColumn('application_stage_version_id');
            $table->foreignUuid('application_id')->constrained();
        });
        Schema::drop('application_stage_versions');
        Schema::drop('application_stages');
        Schema::table('application_hashes', function (Blueprint $table) {
            $table->renameColumn('subsidy_stage_hash_id', 'form_hash_id');
        });
        Schema::table('applications', function (Blueprint $table) {
            $table->renameColumn('subsidy_version_id', 'form_id');
            $table->enum('status', [ApplicationStageVersionStatus::Draft->value, ApplicationStageVersionStatus::Submitted->value]);
            $table->dropColumn('application_title');
            $table->dropColumn('final_review_deadline');
        });
        Schema::create('judgements', function (Blueprint $table) {
            $table->string('judgement')->primary();
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
    }
};
