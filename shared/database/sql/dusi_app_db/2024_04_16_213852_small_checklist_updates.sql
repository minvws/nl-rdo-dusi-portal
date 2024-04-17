UPDATE public.fields
SET params = '{"options": ["Niet van toepassing", "Op het verzekeringsbericht staat vermeld dat de aanvrager een WIA-uitkering ontvangt", "De ingangsdatum van de WIA in de WIA-beslissing komt overeen met de ingangsdatum op het verzekeringsbericht", "De eerste ziektedag ligt in de periode van de eerste golf (1 maart 2020 tot 1 juli 2020)"]}'
WHERE code = 'IVA_Or_WIA_Checklist'
  AND subsidy_stage_id = '8027c102-93ef-4735-ab66-97aa63b836eb';

UPDATE public.fields
SET params = '{"options": ["Niet van toepassing", "De datum waarop de WIA-uitkering niet wordt ontvangen ligt in de periode van 1 maart 2022 tot 1 februari 2023 (104 weken wachttijd)"]}'
WHERE code = 'WIA_RejectedOnHighSalaryChecklist'
  AND subsidy_stage_id = '8027c102-93ef-4735-ab66-97aa63b836eb';
