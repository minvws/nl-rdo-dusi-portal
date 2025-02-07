ALTER TABLE public.subsidy_stage_transitions DROP CONSTRAINT target_application_review_deadline_source_check;

ALTER TABLE public.subsidy_stage_transitions ADD CONSTRAINT target_application_review_deadline_source_check CHECK (((target_application_review_deadline_source)::text = ANY ((ARRAY['field'::character varying, 'existing_deadline'::character varying, 'now'::character varying, 'application_submitted_at'::character varying])::text[])));
