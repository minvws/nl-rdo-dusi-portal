ALTER TABLE public.fields
    ALTER COLUMN params TYPE jsonb USING params::jsonb;

ALTER TABLE public.fields
    ALTER COLUMN required_condition TYPE jsonb USING required_condition::jsonb;

UPDATE public.fields
SET params = params || '{"minItems": 1, "maxItems": 20}'
WHERE "subsidy_stage_id" = '7e5d64e9-35f0-4fee-b8d2-dca967b43183'
  AND "code" = 'certifiedEmploymentDocument';

UPDATE public.fields
SET params = params || '{"minItems": 1, "maxItems": 20}'
WHERE "subsidy_stage_id" = '7e5d64e9-35f0-4fee-b8d2-dca967b43183'
  AND "code" = 'wiaDecisionDocument';

UPDATE public.fields
SET params = params || '{"minItems": 1, "maxItems": 20}'
WHERE "subsidy_stage_id" = '7e5d64e9-35f0-4fee-b8d2-dca967b43183'
  AND "code" = 'wiaDecisionPostponedLetter';

UPDATE public.fields
SET params = params || '{"minItems": 1, "maxItems": 20}'
WHERE "subsidy_stage_id" = '7e5d64e9-35f0-4fee-b8d2-dca967b43183'
  AND "code" = 'employmentContract';

UPDATE public.fields
SET params = params || '{"minItems": 1, "maxItems": 20}'
WHERE "subsidy_stage_id" = '7e5d64e9-35f0-4fee-b8d2-dca967b43183'
  AND "code" = 'otherEmployerDeclarationFile';

UPDATE public.fields
SET params = params || '{"minItems": 1, "maxItems": 20}'
WHERE "subsidy_stage_id" = '7e5d64e9-35f0-4fee-b8d2-dca967b43183'
  AND "code" = 'socialMedicalAssessment';

UPDATE public.fields
SET params = params || '{"minItems": 1, "maxItems": 20}'
WHERE "subsidy_stage_id" = '7e5d64e9-35f0-4fee-b8d2-dca967b43183'
  AND "code" = 'doctorsCertificate';
