<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MinVWS\DUSi\Shared\Application\Models\Connection;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStageDecision;

return new class extends Migration
{

    protected $connection = Connection::APPLICATION;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('answers', function (Blueprint $table) {
            $table->dropColumn('application_stage_version_id');
        });

        Schema::drop('application_stage_versions');
        Schema::drop('application_stages');

        Schema::create('application_stages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('application_id')->constrained();
            $table->mediumInteger('sequence_number', unsigned: true);
            $table->boolean('is_current');
            $table->foreignUuid('subsidy_stage_id')->constrained();
            $table->uuid('assessor_user_id')->nullable();
            $table->enum(
                'assessor_decision',
                ['approved', 'rejected', 'requestForChanges']
            )->nullable();
            $table->timestamps();
            $table->unique(['application_id', 'sequence_number']);
            $table->index(['application_id', 'is_current']);
        });

        Schema::table('answers', function (Blueprint $table) {
            $table->foreignUuid('application_stage_id')->constrained('application_stages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @throws Exception
     */
    public function down(): void
    {
        throw new Exception('No way back!');
    }
};
