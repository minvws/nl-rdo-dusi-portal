<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MinVWS\DUSi\Shared\Subsidy\Models\Connection;

return new class extends Migration
{
    protected $connection = Connection::FORM;

    public function up(): void
    {
        Schema::table('subsidies', function (Blueprint $table) {
            $table->text('description')->nullable();
            $table->date('valid_from');
            $table->date('valid_to')->nullable();
        });

        Schema::table('fields', function (Blueprint $table) {
            $table->string('code', 100)->after('id');
            $table->renameColumn('label', 'title');
            $table->dropColumn('sort');
            $table->enum('source', ['user'])->default('user');
        });

        Schema::create('form_uis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('form_id')->constrained('forms')->cascadeOnDelete();
            $table->unsignedTinyInteger('version');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->json('ui');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('form_uis');

        Schema::table('fields', function (Blueprint $table) {
            $table->renameColumn('title', 'label');
            $table->dropColumn('source');
            $table->unsignedInteger('sort');
        });

        Schema::table('subsidies', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('valid_from');
            $table->dropColumn('valid_to');
        });
    }
};
