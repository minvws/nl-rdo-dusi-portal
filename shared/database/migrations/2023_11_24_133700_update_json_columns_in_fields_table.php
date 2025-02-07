<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Application\Models\Connection;

return new class extends Migration
{
    protected $connection = Connection::APPLICATION;

    public function up(): void
    {
        DB::statement('ALTER TABLE public.fields ALTER COLUMN params TYPE jsonb USING params::jsonb');
        DB::statement('ALTER TABLE public.fields ALTER COLUMN required_condition TYPE jsonb USING params::jsonb');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE public.fields ALTER COLUMN params TYPE json USING params::json');
        DB::statement('ALTER TABLE public.fields ALTER COLUMN required_condition TYPE json USING params::json');
    }
};
