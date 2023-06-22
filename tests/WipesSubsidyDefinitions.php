<?php

namespace Tests;

use App\Models\Connection;
use Illuminate\Support\Facades\DB;

trait WipesSubsidyDefinitions
{

    // add tables in an order that foreign keys won't complain!
    private const SUBSIDY_TABLES = [
        'fields',
        'forms',
        'subsidies'
    ];

    protected function setUpWipesSubsidyDefinitions(): void
    {
        foreach (self::SUBSIDY_TABLES as $table) {
            DB::connection(Connection::Form)->delete("DELETE FROM $table");
        }
    }
}
