<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->enum('status', array_map(fn ($s) => $s->value, ApplicationStatus::cases()))
                ->default(ApplicationStatus::Draft->value);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
