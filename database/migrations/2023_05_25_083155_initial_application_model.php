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
            $table->uuid('application_id');
            $table->string('hash');

            $table->foreign('application_id')->references('id')->on('applications');
            $table->primary(['form_hash_id', 'application_id'], 'id');
        });

        Schema::create('judgements', function(Blueprint $table){
            $table->string('judgement')->primary();
        });

        Schema::create('application_reviews', function(Blueprint $table){
            $table->uuid('id')->primary();
            $table->timestamp('created_at')->useCurrent();
            $table->uuid('application_id');
            $table->uuid('user_id');

            $table->string('judgement');
            $table->string('encrypted_comment');
            $table->string('encryption_key_id');

            $table->foreign('application_id')->references('id')->on('applications');
            $table->foreign('judgement')->references('judgement')->on('judgements');
        });

        Schema::create('answers', function(Blueprint $table){
            $table->uuid('id')->primary();
            $table->uuid('application_id');
            $table->uuid('question_id');
            $table->timestamp('created_at')->useCurrent();

            $table->string('encrypted_answer');
            $table->string('encryption_key_id');

            $table->foreign('application_id')->references('id')->on('applications');
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
