<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MinVWS\DUSi\Shared\Application\Models\Connection;

return new class extends Migration
{
    protected $connection = Connection::APPLICATION;

    public function up(): void
    {
        Schema::create('application_stage_transitions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('application_id')->constrained();
            $table->foreignUuid('subsidy_stage_transition_id')->constrained();
            $table->foreignUuid('previous_application_stage_id')->constrained('application_stages');
            $table->foreignUuid('new_application_stage_id')->nullable()->constrained('application_stages');
            $table->enum(
                'previous_application_status',
                ['draft', 'submitted', 'approved', 'rejected', 'requestForChanges']
            );
            $table->enum(
                'new_application_status',
                ['draft', 'submitted', 'approved', 'rejected', 'requestForChanges']
            );
            $table->timestamp('created_at');
            $table->unique(['application_id', 'previous_application_stage_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_stage_transitions');
    }
};
