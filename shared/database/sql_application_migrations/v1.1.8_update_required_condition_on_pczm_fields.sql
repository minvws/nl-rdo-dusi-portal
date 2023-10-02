BEGIN;

UPDATE public.fields SET required_condition = E'{"type":"comparison","stage":1,"fieldCode":"hasBeenWorkingAtJudicialInstitution","operator":"===","value":"Ja"}' WHERE code = E'BIGNumberJudicialInstitution' AND subsidy_stage_id = '7e5d64e9-35f0-4fee-b8d2-dca967b43183';
UPDATE public.fields SET required_condition = E'{"type":"comparison","stage":1,"fieldCode":"hasPostCovidDiagnose","operator":"===","value":"Nee"}' WHERE code = E'doctorsCertificate' AND subsidy_stage_id = '7e5d64e9-35f0-4fee-b8d2-dca967b43183';
UPDATE public.fields SET required_condition = E'{"type":"or","conditions":[{"type":"comparison","stage":1,"fieldCode":"employmentFunction","operator":"===","value":"Anders"},{"type":"comparison","stage":1,"fieldCode":"employerKind","operator":"===","value":"Andere organisatie"}]}' WHERE code = E'otherEmployerDeclarationFile' AND subsidy_stage_id = '7e5d64e9-35f0-4fee-b8d2-dca967b43183';
UPDATE public.fields SET required_condition = E'{"type":"comparison","stage":1,"fieldCode":"employmentFunction","operator":"===","value":"Anders"}' WHERE code = E'otherEmploymentFunction' AND subsidy_stage_id = '7e5d64e9-35f0-4fee-b8d2-dca967b43183';
UPDATE public.fields SET required_condition = E'{"type":"comparison","stage":1,"fieldCode":"isWiaDecisionPostponed","operator":"===","value":"Ja"}' WHERE code = E'wiaDecisionPostponedLetter' AND subsidy_stage_id = '7e5d64e9-35f0-4fee-b8d2-dca967b43183';

COMMIT;