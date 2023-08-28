<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use MinVWS\DUSi\Shared\Subsidy\Models\Connection;

return new class extends Migration
{
    protected $connection = Connection::FORM;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE fields DROP CONSTRAINT IF EXISTS fields_type_check");

        DB::statement("ALTER TABLE fields ADD CONSTRAINT fields_type_check check (\"type\" in ('text', 'text:numeric', 'text:email', 'text:tel', 'text:url', 'checkbox', 'date', 'multiselect', 'select', 'textarea', 'upload', 'custom:postalcode', 'custom:country', 'custom:bankaccount'));");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE fields DROP CONSTRAINT IF EXISTS fields_type_check");

        DB::statement("ALTER TABLE fields ADD CONSTRAINT fields_type_check check (\"type\" in ('text', 'text:numeric', 'text:email', 'text:tel', 'text:url', 'checkbox', 'multiselect', 'select', 'textarea', 'upload', 'custom:postalcode', 'custom:country', 'custom:bankaccount'));");
    }
};
