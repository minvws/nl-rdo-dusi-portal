ALTER TABLE public.subsidy_stage_transitions
    DROP CONSTRAINT subsidy_stage_transitions_current_subsidy_stage_id_target_subsi,
    ADD COLUMN evaluation_trigger ENUM ('submit', 'expiration') DEFAULT 'submit';

