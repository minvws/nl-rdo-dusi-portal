ALTER TABLE public.fields DROP CONSTRAINT fields_type_check;

ALTER TABLE public.fields
    ADD CONSTRAINT fields_type_check check ("type" in ('text', 'text:numeric', 'text:float', 'text:email', 'text:tel', 'text:url', 'checkbox', 'date', 'multiselect', 'select', 'textarea', 'upload', 'custom:postalcode', 'custom:country', 'custom:bankaccount'));
