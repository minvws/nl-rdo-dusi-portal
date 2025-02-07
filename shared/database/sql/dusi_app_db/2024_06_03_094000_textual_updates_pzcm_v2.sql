-- DUSI-1796 Text changes to Post Covid screens
UPDATE public.subsidies
SET description = 'De regeling Zorgmedewerkers met langdurige post-COVID klachten richt zich op zorgmedewerkers die in de periode van 1 maart 2020 tot en met 31 december 2020 besmet zijn geraakt met COVID-19 en sindsdien langdurige post-COVID klachten hebben. Deze klachten hebben grote invloed op het werk en het privéleven van deze zorgmedewerkers. Zij kunnen soms hun eigen werk als zorgmedewerker niet meer (volledig) doen. Voor deze specifieke groep zorgmedewerkers is een eenmalige financiële ondersteuning van €24.010 beschikbaar.',
    updated_at = 'now()'
WHERE id = '06a6b91c-d59b-401e-a5bf-4bf9262d85f8';

