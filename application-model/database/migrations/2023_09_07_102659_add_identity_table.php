<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\IdentityType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('identities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum(
                'type',
                array_map(fn ($d) => $d->value, IdentityType::cases())
            );
            $table->string('encrypted_identifier');
            $table->string('hashed_identifier')->unique();
            $table->timestamps();
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('identity_identifier');
            $table->dropColumn('identity_type');
            $table->foreignUuid('identity_id')->after('id')->constrained('identities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @throws Exception
     */
    public function down(): void
    {
        throw new Exception('No way back!');
    }
};
