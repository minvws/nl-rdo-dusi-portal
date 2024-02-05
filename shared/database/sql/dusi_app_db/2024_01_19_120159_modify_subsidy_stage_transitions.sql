ALTER TABLE public.subsidy_stage_transitions
    DROP CONSTRAINT subsidy_stage_transitions_current_subsidy_stage_id_target_subsi,
    ADD COLUMN expiration_period INTEGER NULL,
    ADD COLUMN evaluation_trigger VARCHAR(255) NOT NULL DEFAULT 'submit',
    ADD CONSTRAINT "subsidy_stage_transitions_evaluation_trigger_check" CHECK (evaluation_trigger IN ('submit', 'expiration'));
