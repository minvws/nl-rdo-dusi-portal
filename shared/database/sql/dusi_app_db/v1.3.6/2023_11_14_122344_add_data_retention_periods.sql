ALTER TABLE public.subsidies
    ADD COLUMN "short_retention_period" integer NOT NULL DEFAULT '365',
    ADD COLUMN "long_retention_period" integer NOT NULL DEFAULT '2557'
;

ALTER TABLE public.fields
    ADD COLUMN "retention_period_on_approval" varchar(10) CHECK ("retention_period_on_approval" IN ('short', 'long')) NOT NULL DEFAULT 'short';

UPDATE public.fields SET retention_period_on_approval = 'long' WHERE code = 'bankAccountNumber' AND subsidy_stage_id = '7e5d64e9-35f0-4fee-b8d2-dca967b43183';
UPDATE public.fields SET retention_period_on_approval = 'long' WHERE code = 'implementationCoordinatorAssessment' AND subsidy_stage_id = '7e5d64e9-35f0-4fee-b8d2-dca967b43183';
