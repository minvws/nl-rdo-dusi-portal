DELETE
FROM public.subsidy_stage_transitions
where id = '602b66cf-9062-4191-b00f-9530f6a3f87a';

UPDATE public.subsidy_stage_transition_messages
SET subsidy_stage_transition_id = '3a293e03-1de4-47bf-917b-841b7c0a1fff',
    updated_at                  = now()
WHERE id = 'c3b32e69-e093-4f0f-9318-7cc771114f2d';

UPDATE public.subsidy_stage_transitions
SET condition = '{
    "type": "comparison",
    "stage": 3,
    "fieldCode": "secondAssessment",
    "operator": "===",
    "value": "Eens met de eerste beoordeling"
}'
WHERE id = '03c4d9ba-6b82-42da-9ac2-2504f9319a91';

UPDATE public.subsidy_stage_transitions
SET description                   = 'Interne beoording eens met eerste beoordeling',
    condition                     = '{
        "type": "and",
        "conditions": [
            {
                "type": "comparison",
                "stage": 2,
                "fieldCode": "firstAssessment",
                "operator": "===",
                "value": "Afgekeurd"
            },
            {
                "type": "comparison",
                "stage": 4,
                "fieldCode": "internalAssessment",
                "operator": "===",
                "value": "Eens met de eerste beoordeling"
            }
        ]
    }',
    'target_subsidy_stage_id'     = null,
    'send_message'                = true,
    'assign_to_previous_assessor' = false,
    'clone_data'                  = false,
    'target_application_status'   = 'rejected'
WHERE id = '3a293e03-1de4-47bf-917b-841b7c0a1fff';

UPDATE public.subsidy_stage_transitions
SET condition = '{
    "type": "and",
    "conditions": [
        {
            "type": "comparison",
            "stage": 2,
            "fieldCode": "firstAssessment",
            "operator": "===",
            "value": "Goedgekeurd"
        },
        {
            "type": "comparison",
            "stage": 4,
            "fieldCode": "internalAssessment",
            "operator": "===",
            "value": "Eens met de eerste beoordeling"
        }
    ]
}'
WHERE id = '5b876216-ba37-4b13-aa99-e311db027d6b';

INSERT INTO public.subsidy_stage_transitions (id, current_subsidy_stage_id, target_subsidy_stage_id,
                                              target_application_status, condition, send_message, clone_data,
                                              assign_to_previous_assessor, description)
VALUES ('0be7031b-c841-4c27-8104-2d2676d32cff', 'e456e790-1919-4a2b-b3d5-337d0053abe3',
        '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c', null, '{
        "type": "comparison",
        "stage": 4,
        "fieldCode": "internalAssessment",
        "operator": "===",
        "value": "Oneens met de eerste beoordeling"
    }', false, true, true, 'Interne beoordeling oneens met eerste beoordeling');

