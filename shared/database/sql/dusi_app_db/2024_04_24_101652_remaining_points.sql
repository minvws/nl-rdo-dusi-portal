UPDATE public.fields
SET params = '{
    "default": null,
    "options": [
        "Eens met de eerste beoordeling",
        "Oneens met de eerste beoordeling"
    ]
}'
WHERE code = 'secondAssessment'
  AND subsidy_stage_id = 'b2b08566-8493-4560-8afa-d56402931f74';

UPDATE public.subsidy_stage_transitions
SET assign_to_previous_assessor = true
WHERE id = 'cd0491f3-9eef-4094-87fa-ae3babcacd04';
