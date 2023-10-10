BEGIN;

ALTER TABLE public.fields ADD COLUMN required_condition json;

COMMIT;
