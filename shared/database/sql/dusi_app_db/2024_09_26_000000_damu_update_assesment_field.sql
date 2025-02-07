UPDATE public.fields
SET params = '{
    "options": [
        "Woont de aanvrager niet in Caribisch Nederland?",
        "Is het inschrijvingsbewijs van de DAMU-school aangeleverd?",
        "Is naam van de leerling op het DAMU inschrijvingsbewijs hetzelfde als waarvoor subsidie wordt aangevraagd?",
        "Is het inschrijvingsbewijs van de HBO school aangeleverd?",
        "Is naam van de leerling op het HBO inschrijvingsbewijs hetzelfde als waarvoor subsidie wordt aangevraagd?",
        "Is een recente inkomensverklaring (van beide ouders) aangeleverd (maximaal 2 kalenderjaren oud)?"
    ]
}'
WHERE code = 'firstAssessmentChecklist'
  AND subsidy_stage_id = 'fb21ee98-9f58-40b1-9432-fad2937688dc';
