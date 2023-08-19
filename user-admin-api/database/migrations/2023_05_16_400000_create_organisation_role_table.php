<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('organisation_role', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organisation_id')->constrained();
            $table->foreignUuid('user_id')->constrained();
            $table->string('role_name');
            $table->foreign('role_name')->references('name')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organisation_role');
    }
};
