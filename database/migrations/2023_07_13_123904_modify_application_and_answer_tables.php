<?php

declare(strict_types=1);

use App\Models\ApplicationStatus;
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
            $table->enum('status', [ApplicationStatus::Draft->value, ApplicationStatus::Submitted->value]);
            $table->dropColumn('created_at');
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->timestamps();
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
