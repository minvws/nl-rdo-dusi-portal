<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Subsidy\Models\Connection;

trait WipesSubsidyDefinitions
{
    // add tables in an order that foreign keys won't complain!
    private const SUBSIDY_TABLES = [
        'field_subsidy_stage',
        'fields',
        'subsidy_stages',
        'subsidy_versions',
        'subsidies'
    ];

    protected function setUpWipesSubsidyDefinitions(): void
    {
        foreach (self::SUBSIDY_TABLES as $table) {
            DB::connection(Connection::FORM)->delete("DELETE FROM $table");
        }
    }
}
