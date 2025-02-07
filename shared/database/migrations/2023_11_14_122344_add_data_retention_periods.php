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
        Schema::table('subsidies', static function (Blueprint $table) {
            $table->integer('short_retention_period')->default(365);
            $table->integer('long_retention_period')->default(2557);
        });

        Schema::table('fields', static function (Blueprint $table) {
            $table->enum('retention_period_on_approval', ['short', 'long'])->default('short');
        });
    }

    public function down(): void
    {
        Schema::table('subsidies', static function (Blueprint $table) {
            $table->dropColumn('short_retention_period');
            $table->dropColumn('long_retention_period');
        });

        Schema::table('fields', static function (Blueprint $table) {
            $table->dropColumn('retention_period_on_approval');
        });
    }
};
