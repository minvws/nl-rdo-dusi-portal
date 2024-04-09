CREATE TABLE public.application_references
(
    "reference"  varchar(15) NOT NULL,
    created_at timestamp(0) without time zone
);

ALTER TABLE public.application_references OWNER TO postgres;

ALTER TABLE ONLY public.application_references
    ADD CONSTRAINT application_references_reference_unique UNIQUE ("reference");


INSERT INTO public.application_references ("reference", created_at)
SELECT a."reference", a.created_at
FROM public.applications a;
