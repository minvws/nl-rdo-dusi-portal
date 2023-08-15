<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MinVWS\DUSi\Shared\Subsidy\Models\Connection;

return new class extends Migration
{
    protected $connection = Connection::FORM;

    public function up(): void
    {
        Schema::create('subsidy_letters', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('subsidy_version_id')->constrained('subsidy_versions');
            $table->unsignedTinyInteger('version');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->text('content');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('subsidy_letters');
    }
};
