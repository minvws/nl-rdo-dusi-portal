<?php

use App\Models\Enums\ApplicationStageStatus;
use App\Shared\Models\Application\IdentityType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->enum('identity_type', [IdentityType::EncryptedCitizenServiceNumber->value]);
            $table->string('identity_identifier', 200);
            $table->enum('status', [ApplicationStageStatus::DRAFT->value, ApplicationStageStatus::Submitted->value]);
            $table->timestamp('updated_at')->useCurrent();
        });

        Schema::table('answers', function (Blueprint $table) {
            $table->dropColumn('encryption_key_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('identity_type');
            $table->dropColumn('identity_identifier');
            $table->dropColumn('status');
            $table->dropColumn('updated_at');
        });

        Schema::table('answers', function (Blueprint $table) {
            $table->string('encryption_key_id');
        });
    }
};
