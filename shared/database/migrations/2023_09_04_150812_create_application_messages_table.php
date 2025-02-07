<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MinVWS\DUSi\Shared\Application\Models\Connection;

return new class extends Migration {
    private const TABLE = 'application_messages';

    protected $connection = Connection::APPLICATION;

    public function up(): void
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('application_id')->constrained()->cascadeOnDelete();
            $table->string('subject', 200);
            $table->boolean('is_new');
            $table->timestamp('sent_at')->useCurrent();
            $table->timestamp('seen_at')->useCurrentOnUpdate();
            $table->string('html_path', 200);
            $table->string('pdf_path', 200);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::TABLE);
    }
};
