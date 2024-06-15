UPDATE public.fields
SET params = '{"options": ["Niet van toepassing", "Op het verzekeringsbericht staat vermeld dat de aanvrager een WIA-uitkering ontvangt", "De ingangsdatum van de WIA in de WIA-beslissing komt overeen met de ingangsdatum op het verzekeringsbericht", "De eerste ziektedag ligt in de periode van 1 maart 2020 tot en met 31 december 2020"]}'
WHERE code = 'IVA_Or_WIA_Checklist'
  AND subsidy_stage_id = 'e1e5d701-f849-4522-b7ca-75bd4785b1f1';
