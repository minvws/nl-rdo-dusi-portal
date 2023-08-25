<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MinVWS\DUSi\Shared\Subsidy\Models\Connection;

return new class extends Migration
{
    protected $connection = Connection::FORM;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subsidies', function (Blueprint $table) {
            $table->string('code', 50)->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('subsidies', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
};
