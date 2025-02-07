<?php

use Illuminate\Database\Migrations\Migration;
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
        Schema::dropIfExists('organisation_role');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
