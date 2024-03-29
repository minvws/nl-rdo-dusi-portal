ALTER TABLE public.fields DROP CONSTRAINT fields_source_check;

ALTER TABLE public.fields
    ADD CONSTRAINT fields_source_check check ("source" in ('user', 'calculated'));
