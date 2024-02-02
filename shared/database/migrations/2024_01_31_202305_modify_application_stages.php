<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MinVWS\DUSi\Shared\Application\Models\Connection;

return new class extends Migration
{
    protected $connection = Connection::APPLICATION;

    public function up(): void
    {
        Schema::table('application_stages', function (Blueprint $table) {
            $table->date('expires_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('application_stages', function (Blueprint $table) {
            $table->dropColumn('expires_at');
        });
    }
};
