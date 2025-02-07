<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MinVWS\DUSi\Shared\Application\Models\Connection;

return new class extends Migration
{
    protected $connection = Connection::APPLICATION;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subsidy_versions', function (Blueprint $table) {
            $table->dropColumn('review_period');
        });

        Schema::table('subsidy_versions', function (Blueprint $table) {
            $table->unsignedMediumInteger('review_period')->nullable();
            $table->dateTime('review_deadline')->after('review_period')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @throws Exception
     */
    public function down(): void
    {
        Schema::table('subsidy_versions', function (Blueprint $table) {
            $table->dropColumn('review_period');
        });

        Schema::table('subsidy_versions', function (Blueprint $table) {
            $table->unsignedMediumInteger('review_period');
            $table->dropColumn('review_deadline');
        });
    }
};
