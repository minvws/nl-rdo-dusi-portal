<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $enumValues = ['draft', 'pending', 'approved', 'allocated', 'rejected', 'requestForChanges', 'reclaimed'];
        $this->updateEnumConstraint('applications', 'status', $enumValues);
        $this->updateEnumConstraint('subsidy_stage_transitions', 'target_application_status', $enumValues);
        $this->updateEnumConstraint('application_stage_transitions', 'previous_application_status', $enumValues);
        $this->updateEnumConstraint('application_stage_transitions', 'new_application_status', $enumValues);

        Schema::table('subsidy_stages', function (Blueprint $table) {
            $table->boolean('allow_duplicate_assessors')->default(false);
        });
    }

    private function updateEnumConstraint(string $table, string $column, array $allowedValues): void
    {
        $values = implode(', ', array_map(function($value) {
            return "'".$value."'";
        }, $allowedValues));

        DB::statement("ALTER TABLE $table DROP CONSTRAINT {$table}_{$column}_check");
        DB::statement("
            ALTER TABLE $table ADD CONSTRAINT {$table}_{$column}_check
            CHECK ($column IN ({$values}))
        ");
    }

    public function down(): void
    {
        Schema::table('subsidy_stages', function (Blueprint $table) {
            $table->dropColumn('allow_duplicate_assessors');
        });
        $enumValues = ['draft', 'submitted', 'approved', 'rejected', 'requestForChanges'];
        $this->updateEnumConstraint('applications', 'status', $enumValues);
        $this->updateEnumConstraint('subsidy_stage_transitions', 'target_application_status', $enumValues);
        $this->updateEnumConstraint('application_stage_transitions', 'previous_application_status', $enumValues);
        $this->updateEnumConstraint('application_stage_transitions', 'new_application_status', $enumValues);
    }
};
