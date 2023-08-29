<?php

declare(strict_types=1);

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
        Schema::table('application_hashes', function(Blueprint $table) {
           $table->foreign('subsidy_stage_hash_id')->references('id')->on('subsidy_stage_hashes');
        });
        Schema::table('applications', function(Blueprint $table) {
           $table->foreign('subsidy_version_id')->references('id')->on('subsidy_versions');
        });
        Schema::table('application_stages', function(Blueprint $table) {
           $table->foreign('subsidy_stage_id')->references('id')->on('subsidy_stages');
        });
        Schema::table('answers', function(Blueprint $table) {
           $table->foreign('field_id')->references('id')->on('fields');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('application_hashes', function(Blueprint $table) {
            $table->dropForeign('application_hashes_subsidy_stage_hash_id_foreign');
        });
        Schema::table('applications', function(Blueprint $table) {
            $table->dropForeign('applications_subsidy_version_id_foreign');
        });
        Schema::table('application_stages', function(Blueprint $table) {
            $table->dropForeign('application_stages_subsidy_stage_id_foreign');
        });
        Schema::table('answers', function(Blueprint $table) {
            $table->dropForeign('answers_field_id_foreign');
        });
    }
};
