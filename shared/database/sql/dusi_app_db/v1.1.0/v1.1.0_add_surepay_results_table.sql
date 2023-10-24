
CREATE TABLE public.application_surepay_results (
    application_id uuid NOT NULL,
    account_number_validation character varying(20) NOT NULL,
    payment_pre_validation character varying(20) NOT NULL,
    status character varying(20) NOT NULL,
    account_type character varying(20) NOT NULL,
    joint_account boolean,
    number_of_account_holders integer,
    country_code character varying(2) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);

ALTER TABLE public.application_surepay_results OWNER TO postgres;

ALTER TABLE ONLY public.application_surepay_results
    ADD CONSTRAINT application_surepay_results_pkey PRIMARY KEY (application_id);

ALTER TABLE ONLY public.application_surepay_results
    ADD CONSTRAINT application_surepay_results_application_id_foreign FOREIGN KEY (application_id) REFERENCES public.applications(id) ON DELETE CASCADE;
