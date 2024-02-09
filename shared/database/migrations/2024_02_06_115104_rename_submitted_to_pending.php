<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use MinVWS\DUSi\Shared\Application\Models\Connection;

return new class extends Migration
{
    protected $connection = Connection::APPLICATION;

    private const TABLES = [
        'applications' => ['status'],
        'subsidy_stage_transitions' => ['target_application_status'],
        'application_stage_transitions' => ['previous_application_status', 'new_application_status'],
    ];

    private function modifyTables(string $newStatus, string $oldStatus): void
    {
        foreach (self::TABLES as $table => $columns) {
            foreach ($columns as $column) {
                DB::statement("ALTER TABLE $table DROP CONSTRAINT {$table}_{$column}_check");
                DB::update("UPDATE $table SET $column = '$newStatus' WHERE $column = '$oldStatus'");
                DB::statement("
                    ALTER TABLE $table ADD CONSTRAINT {$table}_{$column}_check
                    CHECK ($column IN ('draft', '$newStatus', 'approved', 'rejected', 'requestForChanges'))
                ");
            }
        }
    }

    public function up(): void
    {
        $this->modifyTables('pending', 'submitted');
    }

    public function down(): void
    {
        $this->modifyTables('submitted', 'pending');
    }
};
