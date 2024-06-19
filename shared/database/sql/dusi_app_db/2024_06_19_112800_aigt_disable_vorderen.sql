UPDATE public.fields
SET params = '{
    "default": null,
    "options": [
        "Vaststellen",
        "Uitstellen"
    ]
}'
WHERE code = 'assessment'
  AND subsidy_stage_id = '59ddbc42-8ffc-4e2c-a751-d937714b6df6';

UPDATE public.fields
SET params = '{
    "default": null,
    "options": [
        "Vaststellen",
        "Uitstellen"
    ]
}'
WHERE code = 'assessment'
  AND subsidy_stage_id = '2b06aee1-ea36-41a4-b7ae-74fa53c64a64';
