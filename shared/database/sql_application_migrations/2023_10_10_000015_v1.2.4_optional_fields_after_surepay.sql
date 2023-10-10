BEGIN;

ALTER TABLE public.application_surepay_results
ALTER COLUMN payment_pre_validation DROP NOT NULL,
ALTER COLUMN status DROP NOT NULL,
ALTER COLUMN account_type DROP NOT NULL,
ALTER COLUMN country_code DROP NOT NULL;

COMMIT;
