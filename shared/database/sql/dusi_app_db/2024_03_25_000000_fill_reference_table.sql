CREATE TABLE public.application_references
(
    reference  varchar(15) NOT NULL,
    used       boolean DEFAULT true NOT NULL ,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted    boolean DEFAULT false NOT NULL
);

ALTER TABLE public.application_references OWNER TO postgres;

ALTER TABLE ONLY public.application_references
    ADD CONSTRAINT application_references_reference_unique UNIQUE (reference);


INSERT INTO public.application_references (reference, used, deleted, created_at, updated_at)
SELECT a.reference, true, false, a.created_at, a.created_at
FROM public.applications a;
