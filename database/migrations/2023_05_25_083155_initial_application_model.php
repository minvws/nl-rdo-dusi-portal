<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications', function(Blueprint $table){
            $table->uuid('id')->primary();
            $table->timestamp('created_at')->useCurrent();
            $table->uuid('form_id');
            $table->timestamp('locked_from')->nullable();
        });

        Schema::create('application_hashes', function(Blueprint $table){
            $table->uuid('form_hash_id');
            $table->foreignUuid('application_id')->constrained();
            $table->string('hash');

            $table->primary(['form_hash_id', 'application_id'], 'id');
        });

        Schema::create('judgements', function(Blueprint $table){
            $table->string('judgement')->primary();
        });

        Schema::create('application_reviews', function(Blueprint $table){
            $table->uuid('id')->primary();
            $table->timestamp('created_at')->useCurrent();
            $table->foreignUuid('application_id')->constrained();
            $table->uuid('user_id');

            $table->string('judgement');
            $table->text('encrypted_comment');
            $table->string('encryption_key_id');

            $table->foreign('judgement')->references('judgement')->on('judgements');
        });

        Schema::create('answers', function(Blueprint $table){
            $table->uuid('id')->primary();
            $table->foreignUuid('application_id')->constrained();
            $table->uuid('question_id');
            $table->timestamp('created_at')->useCurrent();

            $table->text('encrypted_answer');
            $table->string('encryption_key_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('applications');
        Schema::drop('application_hashes');
        Schema::drop('judgements');
        Schema::drop('application_reviews');
        Schema::drop('answers');
    }
};
