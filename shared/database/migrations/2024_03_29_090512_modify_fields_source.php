<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use MinVWS\DUSi\Shared\Subsidy\Models\Connection;

return new class extends Migration
{
    protected $connection = Connection::APPLICATION;

    public function up(): void
    {
        DB::statement("ALTER TABLE fields DROP CONSTRAINT IF EXISTS fields_source_check");

        DB::statement("ALTER TABLE fields ADD CONSTRAINT fields_source_check check (\"source\" in ('user', 'calculated'));");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE fields DROP CONSTRAINT IF EXISTS fields_source_check");

        DB::statement("ALTER TABLE fields ADD CONSTRAINT fields_source_check check (\"source\" in ('user'));");
    }

};
