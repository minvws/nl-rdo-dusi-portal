
UPDATE public.fields
SET title = 'Straatnaam'
WHERE subsidy_stage_id = '77996a9c-5c8d-47e1-9a88-e41bf594cfc8'
  AND code = 'street';

UPDATE public.fields
SET title = 'Plaatsnaam'
WHERE subsidy_stage_id = '77996a9c-5c8d-47e1-9a88-e41bf594cfc8'
  AND code = 'city';

UPDATE public.fields
SET title = 'Ontvangt u kinderalimentatie?'
WHERE subsidy_stage_id = '77996a9c-5c8d-47e1-9a88-e41bf594cfc8'
  AND code = 'hasAlimony';

UPDATE public.fields
SET title = 'Straatnaam'
WHERE subsidy_stage_id = '77996a9c-5c8d-47e1-9a88-e41bf594cfc8'
  AND code = 'residentialStreet';

UPDATE public.fields
SET title = 'Plaatsnaam'
WHERE subsidy_stage_id = '77996a9c-5c8d-47e1-9a88-e41bf594cfc8'
  AND code = 'residentialCity';

UPDATE public.fields
SET title = 'Gaat naar het'
WHERE subsidy_stage_id = '77996a9c-5c8d-47e1-9a88-e41bf594cfc8'
  AND code = 'educationType';

UPDATE public.fields
SET required_condition = E'{"type":"comparison","stage":1,"fieldCode":"educationType","operator":"===","value":"Primair onderwijs"}'
WHERE subsidy_stage_id = '77996a9c-5c8d-47e1-9a88-e41bf594cfc8'
  AND code = 'damuSchoolPrimary';

UPDATE public.fields
SET required_condition = E'{"type":"comparison","stage":1,"fieldCode":"educationType","operator":"===","value":"Voortgezet onderwijs"}'
WHERE subsidy_stage_id = '77996a9c-5c8d-47e1-9a88-e41bf594cfc8'
  AND code = 'damuSchoolSecondary';

UPDATE public.fields
SET title = 'DAMU-school adres'
WHERE subsidy_stage_id = '77996a9c-5c8d-47e1-9a88-e41bf594cfc8'
  AND code = 'damuSchoolAddress';

UPDATE public.fields
SET title = 'HBO vooropleiding',
    required_condition = E'{"type":"comparison","stage":1,"fieldCode":"educationType","operator":"===","value":"Primair onderwijs"}'
WHERE subsidy_stage_id = '77996a9c-5c8d-47e1-9a88-e41bf594cfc8'
  AND code = 'hboPreviousEducationPrimary';

UPDATE public.fields
SET title = 'HBO vooropleiding',
    required_condition = E'{"type":"comparison","stage":1,"fieldCode":"educationType","operator":"===","value":"Voortgezet onderwijs"}'
WHERE subsidy_stage_id = '77996a9c-5c8d-47e1-9a88-e41bf594cfc8'
  AND code = 'hboPreviousEducationSecondary';

UPDATE public.fields
SET title = 'Vergoeding per kilometer'
WHERE subsidy_stage_id = '77996a9c-5c8d-47e1-9a88-e41bf594cfc8'
  AND code = 'travelExpenseReimbursement';

UPDATE public.fields
SET title = 'Gevraagd subsidiebedrag'
WHERE subsidy_stage_id = '77996a9c-5c8d-47e1-9a88-e41bf594cfc8'
  AND code = 'requestedSubsidyAmount';

UPDATE public.fields
SET title = 'Inschrijfbewijs hbo-vooropleiding dans en muziek'
WHERE subsidy_stage_id = '77996a9c-5c8d-47e1-9a88-e41bf594cfc8'
  AND code = 'proofOfRegistrationHboCollaborationPartner';
