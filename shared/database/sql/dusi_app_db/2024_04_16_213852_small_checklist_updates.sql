UPDATE public.fields
SET params = '{"options": ["Niet van toepassing", "Op het verzekeringsbericht staat vermeld dat de aanvrager een WIA-uitkering ontvangt", "De ingangsdatum van de WIA in de WIA-beslissing komt overeen met de ingangsdatum op het verzekeringsbericht", "De eerste ziektedag ligt in de periode van de eerste golf (1 maart 2020 tot 1 juli 2020)"]}'
WHERE code = 'IVA_Or_WIA_Checklist'
  AND subsidy_stage_id = '8027c102-93ef-4735-ab66-97aa63b836eb';

UPDATE public.fields
SET params = '{"options": ["Niet van toepassing", "De datum waarop de WIA-uitkering niet wordt ontvangen ligt in de periode van 1 maart 2022 tot 1 februari 2023 (104 weken wachttijd)"]}'
WHERE code = 'WIA_RejectedOnHighSalaryChecklist'
  AND subsidy_stage_id = '8027c102-93ef-4735-ab66-97aa63b836eb';

-- update PCZM intro tekst
UPDATE public.subsidies
SET description = 'De regeling Zorgmedewerkers met langdurige post-COVID klachten richt zich op zorgmedewerkers die tijdens de eerste golf van de COVID-19 pandemie besmet zijn geraakt met COVID-19 en sindsdien langdurige post-COVID klachten hebben. Deze klachten hebben grote invloed op het werk en het privéleven van deze zorgmedewerkers. Zij kunnen soms hun eigen werk als zorgmedewerker niet meer (volledig) doen. Voor deze specifieke groep zorgmedewerkers is een eenmalige financiële ondersteuning van €24.000 beschikbaar.'
WHERE id = '06a6b91c-d59b-401e-a5bf-4bf9262d85f8';

