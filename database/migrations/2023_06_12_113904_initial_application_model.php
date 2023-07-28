<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('subsidies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title', 100);
        });

        Schema::create('forms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('subsidy_id')->constrained('subsidies')->restrictOnDelete();
            $table->unsignedTinyInteger('version');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamps();
        });

        // NOTE:
        // This is just a start. Things like grouping/sections, showing/hiding based on another field,
        // required based on another field etc. should still be added.
        Schema::create('fields', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('form_id')->constrained('forms')->restrictOnDelete();
            $table->string('label', 500);
            $table->mediumText('description')->nullable();
            $table->enum('type', ['text', 'text:numeric', 'text:email', 'text:tel', 'text:url', 'checkbox', 'select', 'textarea', 'upload', 'custom:postalcode', 'custom:country', 'custom:bankaccount']);
            $table->mediumText('params')->nullable();
            $table->boolean('is_required');
            $table->unsignedInteger('sort');
        });

        Schema::create('form_hashes', function(Blueprint $table){
            $table->uuid('id')->primary();
            $table->foreignUuid('form_id')->constrained('forms');
            $table->string('short_description', 200);
        });

        Schema::create('form_hash_fields', function(Blueprint $table){
            $table->foreignUuid('form_hash_id')->constrained('form_hashes');
            $table->foreignUuid('field_id')->constrained('fields');
            $table->primary(['form_hash_id', 'field_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('form_hash_fields');
        Schema::drop('form_hashes');
        Schema::drop('fields');
        Schema::drop('forms');
        Schema::drop('subsidies');
    }
};
