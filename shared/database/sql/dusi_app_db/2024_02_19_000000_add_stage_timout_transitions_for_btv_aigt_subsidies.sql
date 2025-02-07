-- Update AIGT 2 to 1 transition
UPDATE public.subsidy_stage_transitions
SET expiration_period = 14
WHERE id = '2f2e080d-0a05-467a-aaa5-292a95a6d361';

-- Update BTV 2 to 1 transition
UPDATE public.subsidy_stage_transitions
SET expiration_period = 14
WHERE id = 'fc076d68-f51a-4aa7-b190-be0c584d0fca';

-- Create AIGT 1 to 2 timeout transition
INSERT INTO public.subsidy_stage_transitions (
    id, description, current_subsidy_stage_id, target_subsidy_stage_id, target_application_status,
    condition, evaluation_trigger, send_message, assign_to_previous_assessor, clone_data
)
VALUES (
    '947e5c57-a4bc-4613-b41f-f440e52f154c',
    'Geen aanvulling ingediend binnen gestelde termijn',
    'a0f9ed92-c553-42d9-aef6-707bdfadd2d1',
    '7075fcad-7d92-42f6-b46c-7733869019e0',
    'pending',
    null,
    'expiration',
    false,
    true,
    true
);

-- Create BTV 1 to 2 timeout transition
INSERT INTO public.subsidy_stage_transitions (
    id, description, current_subsidy_stage_id, target_subsidy_stage_id, target_application_status,
    condition, evaluation_trigger, send_message, assign_to_previous_assessor, clone_data
)
VALUES (
    '335b7ffc-b439-40a2-9b71-9b3df210216c',
    'Geen aanvulling ingediend binnen gestelde termijn',
    '721c1c28-e674-415f-b1c3-872a631ed045',
    '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c',
    'pending',
    null,
    'expiration',
    false,
    true,
    true
);
