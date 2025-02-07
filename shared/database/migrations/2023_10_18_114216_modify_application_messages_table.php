<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MinVWS\DUSi\Shared\Application\Models\Connection;

return new class extends Migration
{
    protected $connection = Connection::APPLICATION;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('application_messages', function (Blueprint $table) {
            $table->foreignUuid('application_stage_transition_id')->after('application_id')->unique()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @throws Exception
     */
    public function down(): void
    {
        Schema::table('application_messages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('application_stage_transition_id');
        });
    }
};
