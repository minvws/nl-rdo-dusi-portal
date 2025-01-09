<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE public.subsidy_stage_transitions DROP CONSTRAINT subsidy_stage_transitions_target_application_review_deadl_check;");
        DB::statement("ALTER TABLE public.subsidy_stage_transitions ADD CONSTRAINT subsidy_stage_transitions_target_application_review_deadl_check CHECK (((target_application_review_deadline_source)::text = ANY ((ARRAY['field'::character varying, 'existing_deadline'::character varying, 'now'::character varying, 'application_submitted_at'::character varying])::text[])));");
    }
};
