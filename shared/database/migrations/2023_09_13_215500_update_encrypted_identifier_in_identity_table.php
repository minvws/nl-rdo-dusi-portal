<?php

declare(strict_types=1);

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
        Schema::table('identities', function (Blueprint $table) {
            $table->dropColumn('encrypted_identifier');
        });

        Schema::table('identities', function (Blueprint $table) {
            $table->json('encrypted_identifier')->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new Exception('No way back!');
    }
};
