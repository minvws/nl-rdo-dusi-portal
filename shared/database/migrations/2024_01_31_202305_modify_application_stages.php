<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use MinVWS\DUSi\Shared\Application\Models\Connection;

return new class extends Migration
{
    protected $connection = Connection::APPLICATION;

    public function up(): void
    {
        Schema::table('application_stages', function (Blueprint $table) {
            $table->date('expires_at')->nullable();
            $table->datetime('closed_at')->nullable();
        });

        DB::update('UPDATE application_stages SET closed_at = submitted_at WHERE submitted_at IS NOT NULL');
    }

    public function down(): void
    {
        Schema::table('application_stages', function (Blueprint $table) {
            $table->dropColumn('expires_at');
            $table->dropColumn('closed_at');
        });
    }
};
