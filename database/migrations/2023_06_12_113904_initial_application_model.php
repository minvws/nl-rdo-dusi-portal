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

        Schema::create('fields', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('form_id')->constrained('forms')->restrictOnDelete();
            $table->string('label', 500);
            $table->mediumText('description')->nullable();
            $table->enum('type', ['text', 'text:email', 'text:zipcode', 'text:phone', 'checkbox', 'select', 'textarea', 'upload']);
            $table->mediumText('params')->nullable();
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
