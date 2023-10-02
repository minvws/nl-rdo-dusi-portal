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
        Schema::table('application_surepay_results', function (Blueprint $table) {
            $table->string('name_match_result', 20)
                ->nullable()
                ->after('account_number_validation');
        });
    }

    public function down(): void
    {
        Schema::table('application_surepay_results', function (Blueprint $table) {
            $table->dropColumn('name_match_result');
        });
    }
};
