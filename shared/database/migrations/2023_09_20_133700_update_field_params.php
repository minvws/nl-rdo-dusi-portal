<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Application\Models\Connection;

return new class extends Migration
{

    protected $connection = Connection::APPLICATION;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::raw('ALTER TABLE fields ALTER params TYPE JSON USING params::JSON');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::raw('ALTER TABLE fields ALTER params TYPE MEDIUMTEXT');
    }
};
