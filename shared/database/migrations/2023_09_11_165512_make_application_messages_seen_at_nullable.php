<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MinVWS\DUSi\Shared\Application\Models\Connection;

return new class extends Migration {
    private const TABLE = 'application_messages';

    protected $connection = Connection::APPLICATION;

    public function up(): void
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->timestamp('seen_at')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->timestamp('seen_at')->nullable(false)->change();
        });
    }
};