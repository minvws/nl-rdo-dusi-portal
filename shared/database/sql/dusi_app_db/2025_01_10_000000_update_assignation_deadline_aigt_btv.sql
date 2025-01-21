UPDATE public.fields
SET params = '{"readonly": true, "deadlineSource": "existing_deadline", "deadlineOverrideFieldReference": {"stage": 5, "fieldCode": "assignationDeadlineOverride"}}'
WHERE code = 'assignationDeadline' AND subsidy_stage_id = '59ddbc42-8ffc-4e2c-a751-d937714b6df6';

UPDATE public.fields
SET params = '{"readonly": true, "deadlineSource": "existing_deadline", "deadlineOverrideFieldReference": {"stage": 6, "fieldCode": "assignationDeadlineOverride"}}'
WHERE code = 'assignationDeadline' AND subsidy_stage_id = '2b06aee1-ea36-41a4-b7ae-74fa53c64a64';

UPDATE public.fields
SET params = '{"readonly": true, "deadlineSource": "existing_deadline", "deadlineOverrideFieldReference": {"stage": 5, "fieldCode": "assignationDeadlineOverride"}}'
WHERE code = 'assignationDeadline' AND subsidy_stage_id = '1ec333d3-4b9c-437f-a04d-c1f6a7b70446';

UPDATE public.fields
SET params = '{"readonly": true, "deadlineSource": "existing_deadline", "deadlineOverrideFieldReference": {"stage": 6, "fieldCode": "assignationDeadlineOverride"}}'
WHERE code = 'assignationDeadline' AND subsidy_stage_id = 'f5f76ce6-e2ed-4abf-a38e-103b8dfd2b82';
