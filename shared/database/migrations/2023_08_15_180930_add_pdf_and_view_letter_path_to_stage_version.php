<?php

declare(strict_types=1);

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
        Schema::table('application_stage_versions', function (Blueprint $table) {
            $table->string('pdf_letter_path',500)->nullable();
            $table->string('view_letter_path',500)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('application_stage_versions', function (Blueprint $table) {
            $table->dropColumn('pdf_letter_path');
            $table->dropColumn('view_letter_path');
        });
    }
};
