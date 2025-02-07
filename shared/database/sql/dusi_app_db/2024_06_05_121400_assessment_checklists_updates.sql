-- DUSI-1811 Behandelportaal DAMU/AIGT/BTV: Checklist vraag aanpassen naar negatieve vraag stelling
UPDATE public.fields
SET params = '{
    "options": [
        "Aanvrager valt niet onder de WSNP/bewindvoering?",
        "Alle benodigde documenten zijn aangeleverd",
        "Het subsidiebedrag klopt met de gekozen behandeling",
        "De aanvraag kan verleend worden",
        "Het subsidiebedrag is juist vermeld in het Portaal en in de verplichting in SAP",
        "Het IBAN is juist vermeld in het Portaal en in de verplichting in SAP",
        "De verplichting is juist in SAP geboekt",
        "De verplichting is in SAP goedgekeurd",
        "De verleningsbeschikking mag verzonden worden"
    ]
}'
WHERE code = 'internalAssessmentChecklist'
  AND subsidy_stage_id = 'e456e790-1919-4a2b-b3d5-337d0053abe3';

UPDATE public.fields
SET params = '{
    "options": [
        "Aanvrager valt niet onder de WSNP/bewindvoering?",
        "Alle benodigde documenten zijn aangeleverd",
        "De aanvraag kan verleend worden",
        "Het IBAN is juist vermeld in het Portaal en in de verplichting in SAP",
        "De verplichting is juist in SAP geboekt",
        "De verplichting is in SAP goedgekeurd",
        "De verleningsbeschikking mag verzonden worden"
    ]
}'
WHERE code = 'internalAssessmentChecklist'
  AND subsidy_stage_id = '0838f8a9-b2ff-4669-9d42-1c51a1134a34';

UPDATE public.fields
SET params = '{
    "options": [
        "Aanvrager valt niet onder de WSNP/bewindvoering?",
        "Is het subsidiebedrag juist vermeld in SAP?",
        "Komt het IBAN op de aanvraag overeen met SAP?",
        "Is de aangemaakte verplichting geboekt op juiste budgetplaats en budgetpositie?"
    ]
}'
WHERE code = 'internalAssessmentChecklist'
  AND subsidy_stage_id = 'f36ae9b6-1340-453f-8ca7-611bfe9b94cd';

UPDATE public.fields
SET params = '{
    "options": [
        "Aanvrager valt niet onder de WSNP/bewindvoering?",
        "Is de aanvraag tijdig ingediend?",
        "Is het aanvraagformulier volledig ingevuld?",
        "Is het aanvraagformulier juist ondertekend?",
        "Bevat de aanvraag alle vereiste documenten?",
        "Hebben alle ingediende documenten betrekking op de juiste persoon?",
        "Zijn het inschrijvingsbewijs RGS en het opleidingsbewijs OIGT correct ondertekend?",
        "Staat de zakenpartner correct in SAP met het juiste bankrekeningnummer?",
        "Is de einddatum van de buitenlandstage duidelijk?",
        "Komt dit overeen met de opgave van de OIGT?",
        "Komt de aanvrager voor in het M&O-register?"
    ]
}'
WHERE code = 'firstAssessmentChecklist'
  AND subsidy_stage_id = '7075fcad-7d92-42f6-b46c-7733869019e0';
