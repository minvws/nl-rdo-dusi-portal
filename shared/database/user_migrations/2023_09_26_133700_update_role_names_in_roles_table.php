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
            DB::table('roles')->insert(['name' => 'userAdmin']);

            DB::table('role_user')->where('role_name', 'admin')->update(['role_name' => 'userAdmin']);

            DB::table('roles')->where('name', 'admin')->delete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            DB::table('roles')->insert(['name' => 'admin']);

            DB::table('role_user')->where('role_name', 'userAdmin')->update(['role_name' => 'admin']);

            DB::table('roles')->where('name', 'userAdmin')->delete();
        });
    }
};
