delete from public.fields
where code = 'internalAssessmentReasonForRejection' and subsidy_stage_id = 'e456e790-1919-4a2b-b3d5-337d0053abe3';

UPDATE public.fields
SET
    is_required                  = false,
    required_condition           = '{
        "type": "comparison",
        "stage": 2,
        "value": "Goedgekeurd",
        "operator": "===",
        "fieldCode": "firstAssessment"
    }',
    exclude_from_clone_data      = true
WHERE code = 'amount' AND subsidy_stage_id = '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c';
