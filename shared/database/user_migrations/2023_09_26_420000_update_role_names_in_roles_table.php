<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use MinVWS\DUSi\Shared\User\Models\Connection;

return new class extends Migration
{

    protected $connection = Connection::USER;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::transaction(function () {
            DB::table('roles')->insert(['name' => 'assessor']);

            DB::table('role_user')->where('role_name', 'practitioner')->update(['role_name' => 'assessor']);

            DB::table('roles')->where('name', 'practitioner')->delete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            DB::table('roles')->insert(['name' => 'practitioner']);

            DB::table('role_user')->where('role_name', 'assessor')->update(['role_name' => 'practitioner']);

            DB::table('roles')->where('name', 'assessor')->delete();
        });
    }
};
