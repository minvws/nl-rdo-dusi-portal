<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MinVWS\DUSi\Shared\Subsidy\Models\Connection;

return new class extends Migration
{
    protected $connection = Connection::APPLICATION;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::drop('field_subsidy_stage');
        Schema::drop('field_group_uis');
        Schema::drop('field_groups');
        Schema::drop('field_group_purposes');

        Schema::table('fields', function (Blueprint $table) {
            $table->uuid('subsidy_stage_id')->after('id');
            $table->foreign(['subsidy_stage_id'])->references('id')->on('subsidy_stages')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('fields', function (Blueprint $table) {
            $table->dropForeign(['subsidy_stage_id']);
            $table->dropColumn('subsidy_stage_id');
        });

        Schema::create('field_group_purposes', function (Blueprint $table) {
            $table->string('id')->primary();
        });

        schema::create('field_groups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('field_id')->constrained();
            $table->string('purpose');
            $table->foreign('purpose')->references('id')->on('field_group_purposes');
            $table->unsignedTinyInteger('version');
            $table->string('status');
            $table->string('title');
            $table->timestamps();
        });

        Schema::create('field_group_uis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('field_group_id')->constrained();
            $table->unsignedTinyInteger('version');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->json('default_input_ui');
            $table->json('default_view_ui');
            $table->timestamps();
        });

        Schema::create('field_subsidy_stage', function (Blueprint $table){
            $table->primary(['field_id', 'subsidy_stage_id']);
            $table->foreignUuid('field_id')->constrained();
            $table->foreignUuid('subsidy_stage_id')->constrained();
        });
    }
};
