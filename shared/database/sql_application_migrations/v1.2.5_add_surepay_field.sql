BEGIN;

ALTER TABLE public.application_surepay_results
    ADD COLUMN name_match_result VARCHAR(20);

COMMIT;
