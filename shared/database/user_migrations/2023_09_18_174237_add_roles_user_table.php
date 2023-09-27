<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MinVWS\DUSi\Shared\User\Models\Connection;

return new class extends Migration
{

    protected $connection = Connection::USER;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('role_user', function (Blueprint $table) {
            $table->foreignUuid('user_id')->constrained();
            $table->string('role_name');
            $table->foreign('role_name')->references('name')->on('roles');
            $table->uuid('subsidy_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_user');
    }
};
