-- DUSI-1809 Beschikking DAMU: cijfers achter de komma vallen weg bij vastgesteld subsidie bedrag
UPDATE public.fields
SET type = 'text:float'
WHERE code = 'amount'
  AND subsidy_stage_id = 'f343892a-17a8-48e5-81b0-6c3cb710c29a';
