<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MinVWS\DUSi\Shared\Application\Models\Connection;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;

return new class extends Migration
{

    protected $connection = Connection::APPLICATION;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subsidy_stage_transitions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('current_subsidy_stage_id')
                ->constrained('subsidy_stages')
                ->cascadeOnDelete();
            $table->foreignUuid('target_subsidy_stage_id')
                ->nullable()
                ->constrained('subsidy_stages')
                ->cascadeOnDelete();
            $table->enum(
                'target_application_status',
                ['draft', 'submitted', 'approved', 'allocated', 'rejected', 'requestForChanges']
            )->nullable();
            $table->json('condition')->nullable();
            $table->boolean('send_message')->default(false);
            $table->unique(['current_subsidy_stage_id', 'target_subsidy_stage_id']);
        });

        Schema::create('subsidy_stage_transition_messages', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('subsidy_stage_transition_id')->constrained('subsidy_stage_transitions');
            $table->unsignedTinyInteger('version');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->string('subject', 200);
            $table->text('content_html');
            $table->text('content_pdf');
            $table->timestamps();
        });

        Schema::drop('subsidy_letters');

        Schema::table('subsidy_versions', static function (Blueprint $table) {
            $table->dropColumn('message_overview_subject');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @throws Exception
     */
    public function down(): void
    {
        Schema::drop('subsidy_stage_transition_messages');
        Schema::drop('subsidy_stage_transitions');

        Schema::create('subsidy_letters', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('subsidy_version_id')->constrained('subsidy_versions');
            $table->unsignedTinyInteger('version');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->text('content_pdf');
            $table->text('content_view');
            $table->timestamps();
        });

        Schema::table('subsidy_versions', static function (Blueprint $table) {
            $table->string('message_overview_subject');
        });
    }
};
