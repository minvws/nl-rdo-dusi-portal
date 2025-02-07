ALTER TABLE public.subsidy_stages ADD COLUMN assessor_user_role character varying(255);
ALTER TABLE public.subsidy_stages ADD CONSTRAINT subsidy_stages_assessor_user_role_check CHECK (((assessor_user_role)::text = ANY ((ARRAY['userAdmin'::character varying, 'assessor'::character varying, 'implementationCoordinator'::character varying, 'internalAuditor'::character varying])::text[])));
