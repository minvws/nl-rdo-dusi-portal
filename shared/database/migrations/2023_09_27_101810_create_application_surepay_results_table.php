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
        Schema::create('application_surepay_results', function (Blueprint $table) {
            $table->foreignUuid('application_id')->constrained()->cascadeOnDelete();
            $table->string('account_number_validation', 20);
            $table->string('payment_pre_validation', 20);
            $table->string('status', 20);
            $table->string('account_type', 20);
            $table->boolean('joint_account')->nullable();
            $table->integer('number_of_account_holders')->nullable();
            $table->string('country_code', 2);
            $table->timestamps();
            $table->primary('application_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_surepay_results');
    }
};
