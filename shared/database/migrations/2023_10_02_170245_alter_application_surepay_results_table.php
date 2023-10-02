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
            $table->string('payment_pre_validation', 20)->nullable();
            $table->string('status', 20)->nullable();
            $table->string('account_type', 20)->nullable();
            $table->string('country_code', 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('application_surepay_results', function (Blueprint $table) {
            $table->string('payment_pre_validation', 20);
            $table->string('status', 20);
            $table->string('account_type', 20);
            $table->string('country_code', 2);
        });
    }
};
