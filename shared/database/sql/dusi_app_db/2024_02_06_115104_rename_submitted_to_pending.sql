ALTER TABLE public.applications
DROP CONSTRAINT applications_status_check;

UPDATE public.applications
SET status = 'pending'
WHERE status = 'submitted';

ALTER TABLE public.applications
ADD CONSTRAINT applications_status_check
CHECK (status IN ('draft', 'pending', 'approved', 'rejected', 'requestForChanges'));

ALTER TABLE public.subsidy_stage_transitions
DROP CONSTRAINT subsidy_stage_transitions_target_application_status_check;

UPDATE public.subsidy_stage_transitions
SET target_application_status = 'pending'
WHERE target_application_status = 'submitted';

ALTER TABLE public.subsidy_stage_transitions
ADD CONSTRAINT subsidy_stage_transitions_target_application_status_check
CHECK (target_application_status IN ('draft', 'pending', 'approved', 'rejected', 'requestForChanges'));

ALTER TABLE public.application_stage_transitions
DROP CONSTRAINT application_stage_transitions_previous_application_status_check;

UPDATE public.application_stage_transitions
SET previous_application_status = 'pending'
WHERE previous_application_status = 'submitted';

ALTER TABLE public.application_stage_transitions
ADD CONSTRAINT application_stage_transitions_previous_application_status_check
CHECK (previous_application_status IN ('draft', 'pending', 'approved', 'rejected', 'requestForChanges'));

ALTER TABLE public.application_stage_transitions
DROP CONSTRAINT application_stage_transitions_new_application_status_check;

UPDATE application_stage_transitions
SET new_application_status = 'pending'
WHERE new_application_status = 'submitted';

ALTER TABLE public.application_stage_transitions
ADD CONSTRAINT application_stage_transitions_new_application_status_check
CHECK (new_application_status IN ('draft', 'pending', 'approved', 'rejected', 'requestForChanges'));
