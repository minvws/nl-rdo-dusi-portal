BEGIN;

ALTER TABLE public.subsidy_stage_transitions ADD COLUMN assign_to_previous_assessor boolean DEFAULT false NOT NULL;

COMMIT;
