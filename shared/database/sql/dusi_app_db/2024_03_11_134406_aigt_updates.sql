ALTER TABLE public.applications DROP CONSTRAINT IF EXISTS applications_status_check;

ALTER TABLE public.applications ADD CONSTRAINT applications_status_check
CHECK (status IN ('draft', 'pending', 'approved', 'allocated', 'rejected', 'requestForChanges', 'reclaimed'));

ALTER TABLE public.subsidy_stage_transitions DROP CONSTRAINT IF EXISTS subsidy_stage_transitions_target_application_status_check;

ALTER TABLE public.subsidy_stage_transitions ADD CONSTRAINT subsidy_stage_transitions_target_application_status_check
CHECK (target_application_status IN ('draft', 'pending', 'approved', 'allocated', 'rejected', 'requestForChanges', 'reclaimed'));

ALTER TABLE public.application_stage_transitions DROP CONSTRAINT IF EXISTS application_stage_transitions_previous_application_status_check;

ALTER TABLE public.application_stage_transitions ADD CONSTRAINT application_stage_transitions_previous_application_status_check
CHECK (previous_application_status IN ('draft', 'pending', 'approved', 'allocated', 'rejected', 'requestForChanges', 'reclaimed'));

ALTER TABLE public.application_stage_transitions DROP CONSTRAINT IF EXISTS application_stage_transitions_new_application_status_check;

ALTER TABLE public.application_stage_transitions ADD CONSTRAINT application_stage_transitions_new_application_status_check
CHECK (new_application_status IN ('draft', 'pending', 'approved', 'allocated', 'rejected', 'requestForChanges', 'reclaimed'));

alter table public.subsidy_stages add column "allow_duplicate_assessors" boolean not null default '0';
