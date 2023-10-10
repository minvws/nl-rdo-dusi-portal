BEGIN;

UPDATE public.subsidy_stage_transitions
SET clone_data = true::boolean
WHERE id = '870bc38a-0d50-40a9-b49e-d56db5ead6b7'::uuid; -- PZCM_TRANSITION_STAGE_3_TO_1 - RequestForChanges

COMMIT;