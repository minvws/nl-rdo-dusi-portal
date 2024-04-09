<?php

use Illuminate\Database\Migrations\Migration;
use MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\DAMU\DAMUSeeder;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Artisan::call('db:seed', ['--class' => DamuSeeder::class]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
