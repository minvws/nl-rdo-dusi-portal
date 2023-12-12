<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('application_surepay_results', function (Blueprint $table) {
            $table->string('encrypted_name_suggestion', 1000)->after('country_code')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('application_surepay_results', function (Blueprint $table) {
            $table->dropColumn('encrypted_name_suggestion');
        });
    }
};
