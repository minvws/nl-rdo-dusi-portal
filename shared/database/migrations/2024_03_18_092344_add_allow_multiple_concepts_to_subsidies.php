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
            $table->boolean('allow_multiple_applications')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('subsidies', static function (Blueprint $table) {
            $table->dropColumn('allow_multiple_applications');
        });
    }
};
