ALTER TABLE public.subsidy_stage_transitions
    ADD COLUMN target_application_review_deadline_source character varying(255) DEFAULT 'existing_deadline',
    ADD COLUMN target_application_review_deadline_source_field JSONB NULL,
    ADD COLUMN target_application_review_deadline_additional_period VARCHAR(255) NULL;

ALTER TABLE public.subsidy_stage_transitions ADD CONSTRAINT target_application_review_deadline_source_check CHECK (((target_application_review_deadline_source)::text = ANY ((ARRAY['field'::character varying, 'existing_deadline'::character varying, 'now'::character varying])::text[])));
