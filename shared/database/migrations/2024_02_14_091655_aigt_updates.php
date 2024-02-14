<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subsidy_stages', function (Blueprint $table) {
            $table->boolean('allow_duplicate_assessors')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('subsidy_stages', function (Blueprint $table) {
            $table->dropColumn('allow_duplicate_assessors');
        });
    }
};
