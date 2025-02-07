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
            $table->string('payment_pre_validation', 20)->nullable()->change();
            $table->string('status', 20)->nullable()->change();
            $table->string('account_type', 20)->nullable()->change();
            $table->string('country_code', 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('application_surepay_results', function (Blueprint $table) {
            $table->string('payment_pre_validation', 20)->nullable(false)->change();
            $table->string('status', 20)->nullable(false)->change();
            $table->string('account_type', 20)->nullable(false)->change();
            $table->string('country_code', 2)->nullable(false)->change();
        });
    }
};
