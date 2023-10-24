--
-- Dropping constraints from "public"."fields"
--
ALTER TABLE public.fields
DROP CONSTRAINT fields_subsidy_stage_id_foreign;

--
-- Dropping constraints from "public"."subsidy_stage_transition_messages"
--
ALTER TABLE public.subsidy_stage_transition_messages
DROP CONSTRAINT subsidy_stage_transition_messages_subsidy_stage_transition_id_f;

--
-- Dropping constraints from "public"."subsidy_stage_uis"
--
ALTER TABLE public.subsidy_stage_uis
DROP CONSTRAINT form_uis_form_id_foreign;

--
-- Dropping constraints from "public"."subsidy_stages"
--
ALTER TABLE public.subsidy_stages
DROP CONSTRAINT subsidy_stages_subsidy_version_id_foreign;

--
-- Dropping constraints from "public"."subsidy_versions"
--
ALTER TABLE public.subsidy_versions
DROP CONSTRAINT subsidy_versions_subsidy_id_foreign;

--
-- Dropping constraints from "public"."answers"
--
ALTER TABLE public.answers
DROP CONSTRAINT answers_field_id_foreign;

--
-- Dropping constraints from "public"."subsidy_stage_hash_fields"
--
ALTER TABLE public.subsidy_stage_hash_fields
DROP CONSTRAINT form_hash_fields_field_id_foreign;

--
-- Dropping constraints from "public"."application_stages"
--
ALTER TABLE public.application_stages
DROP CONSTRAINT application_stages_subsidy_stage_id_foreign;

--
-- Dropping constraints from "public"."subsidy_stage_hashes"
--
ALTER TABLE public.subsidy_stage_hashes
DROP CONSTRAINT form_hashes_form_id_foreign;

--
-- Dropping constraints from "public"."subsidy_stage_transitions"
--
ALTER TABLE public.subsidy_stage_transitions
DROP CONSTRAINT subsidy_stage_transitions_current_subsidy_stage_id_foreign;

--
-- Dropping constraints from "public"."applications"
--
ALTER TABLE public.applications
DROP CONSTRAINT applications_subsidy_version_id_foreign;

--
-- Deleting data from table "public"."fields"
--
DELETE FROM public.fields WHERE code = E'amount' AND subsidy_stage_id = '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c';
DELETE FROM public.fields WHERE code = E'bankAccountHolder' AND subsidy_stage_id = '721c1c28-e674-415f-b1c3-872a631ed045';
DELETE FROM public.fields WHERE code = E'bankAccountNumber' AND subsidy_stage_id = '721c1c28-e674-415f-b1c3-872a631ed045';
DELETE FROM public.fields WHERE code = E'bankStatement' AND subsidy_stage_id = '721c1c28-e674-415f-b1c3-872a631ed045';
DELETE FROM public.fields WHERE code = E'checklist' AND subsidy_stage_id = '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c';
DELETE FROM public.fields WHERE code = E'checklist' AND subsidy_stage_id = '8027c102-93ef-4735-ab66-97aa63b836eb';
DELETE FROM public.fields WHERE code = E'city' AND subsidy_stage_id = '721c1c28-e674-415f-b1c3-872a631ed045';
DELETE FROM public.fields WHERE code = E'country' AND subsidy_stage_id = '721c1c28-e674-415f-b1c3-872a631ed045';
DELETE FROM public.fields WHERE code = E'dateOfBirth' AND subsidy_stage_id = '721c1c28-e674-415f-b1c3-872a631ed045';
DELETE FROM public.fields WHERE code = E'email' AND subsidy_stage_id = '721c1c28-e674-415f-b1c3-872a631ed045';
DELETE FROM public.fields WHERE code = E'extractPersonalRecordsDatabase' AND subsidy_stage_id = '721c1c28-e674-415f-b1c3-872a631ed045';
DELETE FROM public.fields WHERE code = E'firstName' AND subsidy_stage_id = '721c1c28-e674-415f-b1c3-872a631ed045';
DELETE FROM public.fields WHERE code = E'houseNumber' AND subsidy_stage_id = '721c1c28-e674-415f-b1c3-872a631ed045';
DELETE FROM public.fields WHERE code = E'houseNumberSuffix' AND subsidy_stage_id = '721c1c28-e674-415f-b1c3-872a631ed045';
DELETE FROM public.fields WHERE code = E'infix' AND subsidy_stage_id = '721c1c28-e674-415f-b1c3-872a631ed045';
DELETE FROM public.fields WHERE code = E'lastName' AND subsidy_stage_id = '721c1c28-e674-415f-b1c3-872a631ed045';
DELETE FROM public.fields WHERE code = E'permissionToProcessPersonalData' AND subsidy_stage_id = '721c1c28-e674-415f-b1c3-872a631ed045';
DELETE FROM public.fields WHERE code = E'phoneNumber' AND subsidy_stage_id = '721c1c28-e674-415f-b1c3-872a631ed045';
DELETE FROM public.fields WHERE code = E'postalCode' AND subsidy_stage_id = '721c1c28-e674-415f-b1c3-872a631ed045';
DELETE FROM public.fields WHERE code = E'proofOfMedicalTreatment' AND subsidy_stage_id = '721c1c28-e674-415f-b1c3-872a631ed045';
DELETE FROM public.fields WHERE code = E'proofOfTypeOfMedicalTreatment' AND subsidy_stage_id = '721c1c28-e674-415f-b1c3-872a631ed045';
DELETE FROM public.fields WHERE code = E'review' AND subsidy_stage_id = '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c';
DELETE FROM public.fields WHERE code = E'street' AND subsidy_stage_id = '721c1c28-e674-415f-b1c3-872a631ed045';
DELETE FROM public.fields WHERE code = E'truthfullyCompleted' AND subsidy_stage_id = '721c1c28-e674-415f-b1c3-872a631ed045';

--
-- Inserting data into table "public"."fields"
--
INSERT INTO public.fields(code, subsidy_stage_id, id, title, description, type, params, is_required, source) VALUES
    (E'applicantFoundInBigRegister', '8027c102-93ef-4735-ab66-97aa63b836eb', 'c5923264-53ed-4dce-bfb3-473bb9e83672', E'De aanvrager is op basis van het doorgegeven BIG-nummer terug te vinden in het BIG-register', NULL, E'select', E'{"options":["Nee","Ja","Niet van toepassing"],"default":null}', false, E'user'),
    (E'chamberOfCommerceNumberHealthcareProvider', '8027c102-93ef-4735-ab66-97aa63b836eb', '3d6a9efa-f193-461b-ba96-d929db5819a4', E'KVK-nummer van de zorgaanbieder waar de zorg is verleend', NULL, E'text', E'{"maxLength":null}', false, E'user'),
    (E'coordinatorImplementationApprovalNote', '85ed726e-cdbe-444e-8d12-c56f9bed2621', '7433f48a-c39c-4efa-929e-0d41f7f321de', E'Extra informatie over de gedane wijzigingen', NULL, E'text', E'{"maxLength":null}', false, E'user'),
    (E'coordinatorImplementationInternalNote', '85ed726e-cdbe-444e-8d12-c56f9bed2621', '779f3b5c-d9fa-4407-ab3d-028fe1852506', E'Interne notitie', NULL, E'text', E'{"maxLength":null}', false, E'user'),
    (E'coordinatorImplementationReasonForRejection', '85ed726e-cdbe-444e-8d12-c56f9bed2621', '64edbbfd-4330-44f1-94fc-fe0bc3a7ee8b', E'Reden van afkeuring', NULL, E'text', E'{"maxLength":null}', false, E'user'),
    (E'doctorFoundInBigRegister', '8027c102-93ef-4735-ab66-97aa63b836eb', '80189e39-ee0a-454d-9da9-ab50c7ae192b', E'De arts die de verklaring heeft afgegeven is als arts geregistreerd in het BIG-register', NULL, E'select', E'{"options":["Nee","Ja","Niet van toepassing"],"default":null}', false, E'user'),
    (E'doctorsCertificateIsComplete', '8027c102-93ef-4735-ab66-97aa63b836eb', '4bd1cfc6-e6ef-46f5-9976-3e8b34e0a275', E'De verklaring van de arts is volledig ingevuld', NULL, E'select', E'{"options":["Nee","Ja","Niet van toepassing"],"default":null}', false, E'user'),
    (E'employerChecklist', '8027c102-93ef-4735-ab66-97aa63b836eb', '9bcc00c3-38ff-4cdd-a4bc-bbbe776f2949', E'Controlevragen', NULL, E'multiselect', E'{"options":["De werkgever komt overeen met de laatste werkgever v\\u00f3\\u00f3r de WIA in het verzekeringsbericht","Uit de arbeidsovereenkomst en\\/of de verklaring van de zorgaanbieder blijkt dat er sprake is van werkzaamheden die binnen de subsidieregeling vallen"]}', false, E'user'),
    (E'employerName', '8027c102-93ef-4735-ab66-97aa63b836eb', 'acd13a52-db1e-4c1b-a698-43275e9c31a1', E'Naam werkgever', NULL, E'text', E'{"maxLength":null}', false, E'user'),
    (E'firstAssessmentInternalNote', '8027c102-93ef-4735-ab66-97aa63b836eb', 'ab419d7a-cba9-407b-b061-2b604c6add3e', E'Interne notitie', NULL, E'text', E'{"maxLength":null}', false, E'user'),
    (E'firstAssessmentRejectedNote', '8027c102-93ef-4735-ab66-97aa63b836eb', '9ec74b36-1883-477d-a609-02bca8036dfa', E'Reden van afkeuring', NULL, E'text', E'{"maxLength":null}', false, E'user'),
    (E'firstAssessmentRequestedComplementNote', '8027c102-93ef-4735-ab66-97aa63b836eb', '7fe902ce-60a3-4eb6-aa90-a30cfaf758aa', E'Toelichting van benodigde aanvullingen', NULL, E'text', E'{"maxLength":null}', false, E'user'),
    (E'firstAssessmentRequestedComplementReason', '8027c102-93ef-4735-ab66-97aa63b836eb', '63424969-588b-4f2c-996b-c809d52f020d', E'Reden', NULL, E'select', E'{"options":["Incomplete aanvraag","Onduidelijkheid of vervolgvragen"],"default":null}', false, E'user'),
    (E'firstAssessorMotivatedValid', '61436439-e337-4986-bc18-57138e2fab65', '22e48a7f-24d9-4204-bd1e-d6ded972e103', E'De motivatie van de eerste behandelaar is duidelijk en correct', NULL, E'checkbox', E'null', false, E'user'),
    (E'firstSickDayWithinExpiryDate', '8027c102-93ef-4735-ab66-97aa63b836eb', 'b1fa66e3-c364-49d3-a545-8bcf3ad0c874', E'Uit de toekenningsbrief van de afgesproken loondoorbetaling blijkt dat de eerste ziektedag in de periode van 1 maart 2020 en 1 juli 2020 ligt', NULL, E'select', E'{"options":["Nee","Ja","Niet van toepassing"],"default":null}', false, E'user'),
    (E'healthcareProviderAGBCode', '8027c102-93ef-4735-ab66-97aa63b836eb', '0addf2eb-4bb3-4e49-9117-7b574b5b3315', E'AGB-code zorgaanbieder', NULL, E'text', E'{"maxLength":100}', false, E'user'),
    (E'healthcareProviderChecklist', '8027c102-93ef-4735-ab66-97aa63b836eb', '2b3c74f3-abd6-48e0-b2b2-a2b3866b54e5', E'Controlevragen', NULL, E'multiselect', E'{"options":["De zorgaanbieder waar de aanvrager werkzaam is geweest heeft de juiste SBI-code","De zorgaanbieder waar de aanvrager werkzaam is geweest heeft de juiste AGB code of is een Jeugdhulp aanbieder die op de lijst staat","De zorgaanbieder voldoet aan de eisen binnen de regeling"]}', false, E'user'),
    (E'healthcareProviderName', '8027c102-93ef-4735-ab66-97aa63b836eb', '204481dc-3252-4d23-a45c-30f7f7a1751c', E'Naam zorgaanbieder, indien niet werkgever', NULL, E'text', E'{"maxLength":null}', false, E'user'),
    (E'healthcareProviderSBICode', '8027c102-93ef-4735-ab66-97aa63b836eb', 'e19b42a2-f2a6-45e7-9a4e-613f5674161f', E'SBI-code zorgaanbieder', NULL, E'text', E'{"maxLength":100}', false, E'user'),
    (E'healthcareProviderStatementIsComplete', '8027c102-93ef-4735-ab66-97aa63b836eb', 'edf38b47-c536-4783-afde-7ff759daa664', E'De verklaring van de zorgaanbieder is volledig ingevuld', NULL, E'select', E'{"options":["Nee","Ja","Niet van toepassing"],"default":null}', false, E'user'),
    (E'internalAssessmentChecklist', '7ceb3c91-5c3b-4627-b9ef-a46d5fe2ed68', '9f4b6cc7-10bc-48f7-b13b-2389d7b4487d', E'Controlevragen', NULL, E'multiselect', E'{"options":["Alle benodigde documenten zijn aangeleverd","Uit de dataverificatie blijkt dat er geen onvolkomenheden zijn geconstateerd","De motivatie van de eerste beoordeling is duidelijk"]}', false, E'user'),
    (E'internalAssessmentInternalNote', '7ceb3c91-5c3b-4627-b9ef-a46d5fe2ed68', '2d355169-dc1b-41de-9a73-c5e0d2f28da8', E'Interne notitie', NULL, E'text', E'{"maxLength":null}', false, E'user'),
    (E'IVA_Or_WIA_Checklist', '8027c102-93ef-4735-ab66-97aa63b836eb', '6fd6ae8a-1528-42a4-883b-f8864037d301', E'IVA- of WGA-uitkering', NULL, E'multiselect', E'{"options":["Niet van toepassing","Op het verzekeringsbericht staat vermeld dat de aanvrager een WIA-uitkering ontvangt","De ingangsdatum van de WIA in de WIA-beslissing komt overeen met de ingangsdatum op het verzekeringsbericht","De eerste ziektedag ligt in de periode van de eerste golf (1 maart 2020 tot 1 juli 2020)"]}', false, E'user'),
    (E'judicialInstitutionIsEligible', '8027c102-93ef-4735-ab66-97aa63b836eb', 'ac0ab67b-c726-461b-b8cb-57b45cfc7715', E'De justitiële inrichting waar de aanvrager werkzaam is geweest valt binnen de regeling', NULL, E'select', E'{"options":["Nee","Ja","Niet van toepassing"],"default":null}', false, E'user'),
    (E'personalDataChecklist', '8027c102-93ef-4735-ab66-97aa63b836eb', '9f608415-aa73-47a8-9c37-41373b59fb49', E'Controlevragen', NULL, E'multiselect', E'{"options":["Alle aangeleverde documenten zijn te herleiden tot dezelfde persoon op basis van BSN en de overige persoonsgegevens","Het IBAN bestaat en is actief","Het opgegeven IBAN staat op naam van de aanvrager of bewindvoerder","Op basis van de SurePay terugkoppeling ben ik akkoord met het opgegeven rekeningnummer"]}', false, E'user'),
    (E'postCovidChecklist', '8027c102-93ef-4735-ab66-97aa63b836eb', '239c909a-b0fd-4017-adc7-04b2ac47283a', E'Controlevragen', NULL, E'multiselect', E'{"options":["Op basis van het sociaal-medisch verslag en\\/of de verklaring van de arts is vast te stellen dat er een post-COVID diagnose is gesteld","De post-COVID diagnose is v\\u00f3\\u00f3r 1 juni 2023 gesteld"]}', false, E'user'),
    (E'secondAssessmentInternalNote', '61436439-e337-4986-bc18-57138e2fab65', '90b407b4-d1b3-4084-9ea1-3ff1c56138fb', E'Interne notitie', NULL, E'text', E'{"maxLength":null}', false, E'user'),
    (E'wiaChecklist', '8027c102-93ef-4735-ab66-97aa63b836eb', '3a760425-81b4-48bf-9cea-757093b71fb1', E'Algemeen', NULL, E'multiselect', E'{"options":["Het verzekeringsbericht is gewaarmerkt en het BSN is zichtbaar in de upload","Het BSN op het verzekeringsbericht komt overeen met dat van de aanvrager"]}', false, E'user'),
    (E'WIADecisionIndicates', '8027c102-93ef-4735-ab66-97aa63b836eb', 'b2c72568-ed58-4eec-b0f8-25bb44ed0599', E'Uit de WIA-beslissing blijkt dat er sprake is van', NULL, E'select', E'{"options":["IVA uitkering","WGA uitkering","Geen WIA-uitkering met als reden dat meer dan 65% verdiend kan worden"],"default":null}', false, E'user'),
    (E'WIA_RejectedOnHighSalaryChecklist', '8027c102-93ef-4735-ab66-97aa63b836eb', '165328ad-ac57-4494-adef-b0f3ecaff5e4', E'Geen WIA-uitkering met als reden dat meer dan 65% verdiend kan worden', NULL, E'multiselect', E'{"options":["Niet van toepassing","De datum waarop de WIA-uitkering niet wordt ontvangen ligt in de periode van 1 maart 2022 tot 1 september 2022 (104 weken wachttijd)"]}', false, E'user');

--
-- Inserting data into table "public"."migrations"
--
INSERT INTO public.migrations(id, migration, batch) VALUES
    (36, E'2023_09_22_152045_modify_fields_table', 1),
    (37, E'2023_09_27_101810_create_application_surepay_results_table', 1),
    (38, E'2023_09_27_140312_modify_subsidy_stages_table', 1);

--
-- Deleting data from table "public"."subsidies"
--
DELETE FROM public.subsidies WHERE id = '00f26400-7232-475f-922c-6b569b7e421a';
DELETE FROM public.subsidies WHERE id = '3f0b3cdc-937f-4de3-bb89-3f84ee31221a';
DELETE FROM public.subsidies WHERE id = 'a320abc3-6913-4da8-a803-6cf49b2b25e5';

--
-- Updating data of table "public"."subsidies"
--
UPDATE public.subsidies SET title = E'Zorgmedewerkers met langdurige post-COVID klachten' WHERE id = '06a6b91c-d59b-401e-a5bf-4bf9262d85f8';

--
-- Updating data of table "public"."subsidy_stage_transition_messages"
--
UPDATE public.subsidy_stage_transition_messages SET content_html = E'{block content}\r\n    <p>Beste lezer,</p>\r\n    <p>\r\n        Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.\r\n        Met deze brief beslis ik op uw aanvraag.\r\n    </p>\r\n    <p>&nbsp;</p>\r\n\r\n    <h2>Besluit</h2>\r\n    <p>Uw aanvraag moet ik helaas afwijzen. De reden(en) licht ik hieronder toe.</p>\r\n    <p>&nbsp;</p>\r\n\r\n    {if $content->motivation}\r\n        <h2>Motivering bij het besluit</h2>\r\n        <p>Uw aanvraag wordt afgewezen vanwege de volgende reden(en):</p>\r\n        <p>{$content->motivation}</p>\r\n        <p>&nbsp;</p>\r\n    {/if}\r\n{/block}\r\n', content_pdf = E'{layout ''letter_layout.latte''}\r\n\r\n{block concern}\r\n    Betreft: Afwijzing aanvraag {$content->subsidyTitle}\r\n{/block}\r\n\r\n{block content}\r\n    <p>Beste lezer,</p>\r\n    <p>\r\n        Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.\r\n        Met deze brief beslis ik op uw aanvraag.\r\n    </p>\r\n    <p>&nbsp;</p>\r\n\r\n    <h2>Besluit</h2>\r\n    <p>Uw aanvraag moet ik helaas afwijzen. De reden(en) licht ik hieronder toe.</p>\r\n    <p>&nbsp;</p>\r\n\r\n    {if $content->motivation}\r\n        <h2>Motivering bij het besluit</h2>\r\n        <p>Uw aanvraag wordt afgewezen vanwege de volgende reden(en):</p>\r\n        <p>{$content->motivation}</p>\r\n        <p>&nbsp;</p>\r\n    {/if}\r\n{/block}\r\n\r\n{block sidebar}\r\n    {include parent}\r\n{/block}\r\n', created_at = '2023-09-28 20:16:38' WHERE id = '64a636d8-ed0c-4bb6-982e-f948c68755b6';
UPDATE public.subsidy_stage_transition_messages SET content_html = E'{block content}\r\n    <p>Beste lezer,</p>\r\n    <p>\r\n        Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.\r\n        Met deze brief beslis ik op uw aanvraag.\r\n    </p>\r\n    <p>&nbsp;</p>\r\n\r\n    <h2>Besluit</h2>\r\n    <p>Uw aanvraag moet ik helaas afwijzen. De reden(en) licht ik hieronder toe.</p>\r\n    <p>&nbsp;</p>\r\n\r\n    {if $content->motivation}\r\n        <h2>Motivering bij het besluit</h2>\r\n        <p>Uw aanvraag wordt afgewezen vanwege de volgende reden(en):</p>\r\n        <p>{$content->motivation}</p>\r\n        <p>&nbsp;</p>\r\n    {/if}\r\n{/block}\r\n', content_pdf = E'{layout ''letter_layout.latte''}\r\n\r\n{block concern}\r\n    Betreft: Afwijzing aanvraag {$content->subsidyTitle}\r\n{/block}\r\n\r\n{block content}\r\n    <p>Beste lezer,</p>\r\n    <p>\r\n        Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.\r\n        Met deze brief beslis ik op uw aanvraag.\r\n    </p>\r\n    <p>&nbsp;</p>\r\n\r\n    <h2>Besluit</h2>\r\n    <p>Uw aanvraag moet ik helaas afwijzen. De reden(en) licht ik hieronder toe.</p>\r\n    <p>&nbsp;</p>\r\n\r\n    {if $content->motivation}\r\n        <h2>Motivering bij het besluit</h2>\r\n        <p>Uw aanvraag wordt afgewezen vanwege de volgende reden(en):</p>\r\n        <p>{$content->motivation}</p>\r\n        <p>&nbsp;</p>\r\n    {/if}\r\n{/block}\r\n\r\n{block sidebar}\r\n    {include parent}\r\n{/block}\r\n', created_at = '2023-09-28 20:16:38' WHERE id = '7da32b2f-4f0d-44ab-bc87-07718db4bfd5';
UPDATE public.subsidy_stage_transition_messages SET content_html = E'{block content}\r\n    <p>Beste lezer,</p>\r\n    <p>\r\n        Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.\r\n    </p>\r\n\r\n    <p>Om de aanvraag goed te kunnen beoordelen ontvang ik graag van u de volgende aanvullende informatie:</p>\r\n    <p>{$content->motivation}</p>\r\n    <p>&nbsp;</p>\r\n\r\n    <h2>Termijn</h2>\r\n    <p>Ik ontvang de informatie graag binnen 2 weken na dagtekening van deze brief, dus uiterlijk *DocumentDatum+14dagen*. U kunt deze toevoegen aan uw aanvraag door het door u ingevulde aanvraagformulier te heropenen in Mijn DUS-I.</p>\r\n    <p>Als de gevraagde gegevens niet binnen de gestelde termijn van 2 weken door mij zijn ontvangne, of onvoldoende zijn voor de beoordeling, kan ik niet op tijd een besluit nemen op uw aanvraag.</p>\r\n    <p>&nbsp;</p>\r\n{/block}\r\n', content_pdf = E'{layout ''letter_layout.latte''}\r\n\r\n{block concern}\r\n    Betreft: Vragen over aanvraag {$content->subsidyTitle}\r\n{/block}\r\n\r\n{block content}\r\n    <p>Beste lezer,</p>\r\n    <p>\r\n        Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.\r\n    </p>\r\n\r\n    <p>Om de aanvraag goed te kunnen beoordelen ontvang ik graag van u de volgende aanvullende informatie:</p>\r\n    <p>{$content->motivation}</p>\r\n    <p>&nbsp;</p>\r\n\r\n    <h2>Termijn</h2>\r\n    <p>Ik ontvang de informatie graag binnen 2 weken na dagtekening van deze brief, dus uiterlijk *DocumentDatum+14dagen*. U kunt deze toevoegen aan uw aanvraag door het door u ingevulde aanvraagformulier te heropenen in Mijn DUS-I.</p>\r\n    <p>Als de gevraagde gegevens niet binnen de gestelde termijn van 2 weken door mij zijn ontvangne, of onvoldoende zijn voor de beoordeling, kan ik niet op tijd een besluit nemen op uw aanvraag.</p>\r\n    <p>&nbsp;</p>\r\n{/block}\r\n\r\n{block sidebar}\r\n    {include parent}\r\n{/block}\r\n', created_at = '2023-09-28 20:16:38' WHERE id = '85bf054e-c6e3-42d2-880d-07c29d0fe6bf';
UPDATE public.subsidy_stage_transition_messages SET content_html = E'{block content}\r\n    <p>Beste lezer,</p>\r\n    <p>\r\n        Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.\r\n        Met deze brief beslis ik op uw aanvraag.\r\n    </p>\r\n    <p>&nbsp;</p>\r\n\r\n    <h2>Besluit</h2>\r\n    <p>Hierbij verleen ik u de financiële tegemoetkoming/subsidie van {$content->stage2->amount}.</p>\r\n    <p>&nbsp;</p>\r\n\r\n    {if $content->motivation}\r\n        <h2>Motivering bij het besluit</h2>\r\n        <p>Uw aanvraag wordt verleend vanwege de volgende reden(en):</p>\r\n        <p>{$content->motivation}</p>\r\n        <p>&nbsp;</p>\r\n    {/if}\r\n{/block}\r\n', content_pdf = E'{layout ''letter_layout.latte''}\r\n\r\n{block concern}\r\n    Betreft: Verlening aanvraag {$content->subsidyTitle}\r\n{/block}\r\n\r\n{block content}\r\n    <p>Beste lezer,</p>\r\n    <p>\r\n        Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.\r\n        Met deze brief beslis ik op uw aanvraag.\r\n    </p>\r\n    <p>&nbsp;</p>\r\n\r\n    <h2>Besluit</h2>\r\n    <p>Hierbij verleen ik u de financiële tegemoetkoming/subsidie van {$content->stage2->amount}.</p>\r\n    <p>&nbsp;</p>\r\n\r\n    {if $content->motivation}\r\n        <h2>Motivering bij het besluit</h2>\r\n        <p>Uw aanvraag wordt verleend vanwege de volgende reden(en):</p>\r\n        <p>{$content->motivation}</p>\r\n        <p>&nbsp;</p>\r\n    {/if}\r\n{/block}\r\n\r\n{block sidebar}\r\n    {include parent}\r\n{/block}\r\n', created_at = '2023-09-28 20:16:38' WHERE id = '9c2ad81e-cf52-41a3-966f-fc9757de15c9';

--
-- Deleting data from table "public"."subsidy_stage_uis"
--
DELETE FROM public.subsidy_stage_uis WHERE id = '72475863-7987-4375-94d7-21e04ff6552b';
DELETE FROM public.subsidy_stage_uis WHERE id = 'c2365ef6-5ff9-469f-ab4a-5533c33b299d';

--
-- Updating data of table "public"."subsidy_stage_uis"
--
UPDATE public.subsidy_stage_uis SET input_ui = E'{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\\/properties\\/firstAssessorMotivatedValid","options":{"format":"checkbox"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\\/properties\\/secondAssessment","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\\/properties\\/secondAssessmentInternalNote","options":[]}]}]}]}', view_ui = E'{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"CustomControl","scope":"#\\/properties\\/firstAssessorMotivatedValid","options":{"readonly":true}},{"type":"CustomControl","scope":"#\\/properties\\/secondAssessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\\/properties\\/secondAssessmentInternalNote","options":{"readonly":true}}]}' WHERE id = '44914bc7-9e4f-4b79-9498-01adbe5c4cfe';
UPDATE public.subsidy_stage_uis SET input_ui = E'{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Persoonsgegevens","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\\/properties\\/personalDataChecklist","options":{"format":"checkbox-group"}}]}]},{"type":"Group","label":"Vaststellen WIA","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\\/properties\\/wiaChecklist","options":{"format":"checkbox-group"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\\/properties\\/WIADecisionIndicates","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\\/properties\\/firstSickDayWithinExpiryDate","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\\/properties\\/IVA_Or_WIA_Checklist","options":{"format":"checkbox-group"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\\/properties\\/WIA_RejectedOnHighSalaryChecklist","options":{"format":"checkbox-group"}}]}]},{"type":"Group","label":"Zorgaanbieder en functie","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\\/properties\\/employerChecklist","options":{"format":"checkbox-group"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\\/properties\\/healthcareProviderStatementIsComplete","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\\/properties\\/employerName","options":[]}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\\/properties\\/healthcareProviderName","options":[]}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\\/properties\\/chamberOfCommerceNumberHealthcareProvider","options":[]}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\\/properties\\/healthcareProviderChecklist","options":{"format":"checkbox-group"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\\/properties\\/healthcareProviderSBICode","options":[]}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\\/properties\\/healthcareProviderAGBCode","options":[]}]}]},{"type":"Group","label":"Justiti\\u00eble inrichting","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\\/properties\\/judicialInstitutionIsEligible","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\\/properties\\/applicantFoundInBigRegister","options":{"format":"radio"}}]}]},{"type":"Group","label":"Vaststellen post-COVID","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\\/properties\\/postCovidChecklist","options":{"format":"checkbox-group"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\\/properties\\/doctorFoundInBigRegister","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\\/properties\\/doctorsCertificateIsComplete","options":{"format":"radio"}}]}]},{"type":"Group","label":"Uitkering","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\\/properties\\/amount","options":{"format":"radio"}}]}]},{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\\/properties\\/firstAssessment","options":{"format":"radio"}}]}]},{"type":"Group","label":"Toelichting","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\\/properties\\/firstAssessmentRequestedComplementReason","options":{"format":"radio"},"rule":{"effect":"SHOW","condition":{"scope":"#\\/properties\\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\\/properties\\/firstAssessmentRequestedComplementNote","options":{"tip":"Toelichting: Deze notitie wordt opgenomen binnen de brief aan de aanvrager."},"rule":{"effect":"SHOW","condition":{"scope":"#\\/properties\\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\\/properties\\/firstAssessmentRejectedNote","options":{"tip":"Toelichting: Deze notitie wordt opgenomen binnen de brief aan de aanvrager."},"rule":{"effect":"SHOW","condition":{"scope":"#\\/properties\\/firstAssessment","schema":{"const":"Afgekeurd"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\\/properties\\/firstAssessmentInternalNote","options":[]}]}]}]}', view_ui = E'{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"FormGroupControl","label":"Persoonsgegevens","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\\/properties\\/personalDataChecklist","options":{"readonly":true,"format":"checkbox-group"}}]},{"type":"FormGroupControl","label":"Vaststellen WIA","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\\/properties\\/wiaChecklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#\\/properties\\/WIADecisionIndicates","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\\/properties\\/firstSickDayWithinExpiryDate","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\\/properties\\/IVA_Or_WIA_Checklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#\\/properties\\/WIA_RejectedOnHighSalaryChecklist","options":{"readonly":true,"format":"checkbox-group"}}]},{"type":"FormGroupControl","label":"Zorgaanbieder en functie","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\\/properties\\/employerChecklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#\\/properties\\/healthcareProviderStatementIsComplete","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\\/properties\\/employerName","options":{"readonly":true}},{"type":"CustomControl","scope":"#\\/properties\\/healthcareProviderName","options":{"readonly":true}},{"type":"CustomControl","scope":"#\\/properties\\/chamberOfCommerceNumberHealthcareProvider","options":{"readonly":true}},{"type":"CustomControl","scope":"#\\/properties\\/healthcareProviderChecklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#\\/properties\\/healthcareProviderSBICode","options":{"readonly":true}},{"type":"CustomControl","scope":"#\\/properties\\/healthcareProviderAGBCode","options":{"readonly":true}}]},{"type":"FormGroupControl","label":"Justiti\\u00eble inrichting","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\\/properties\\/judicialInstitutionIsEligible","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\\/properties\\/applicantFoundInBigRegister","options":{"readonly":true,"format":"radio"}}]},{"type":"FormGroupControl","label":"Vaststellen post-COVID","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\\/properties\\/postCovidChecklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#\\/properties\\/doctorFoundInBigRegister","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\\/properties\\/doctorsCertificateIsComplete","options":{"readonly":true,"format":"radio"}}]},{"type":"FormGroupControl","label":"Eerste beoordeling","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\\/properties\\/amount","options":{"readonly":true}},{"type":"CustomControl","scope":"#\\/properties\\/firstAssessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\\/properties\\/firstAssessmentRequestedComplementReason","options":{"readonly":true,"format":"radio"},"rule":{"effect":"SHOW","condition":{"scope":"#\\/properties\\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}},{"type":"CustomControl","scope":"#\\/properties\\/firstAssessmentRequestedComplementNote","options":{"readonly":true},"rule":{"effect":"SHOW","condition":{"scope":"#\\/properties\\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}},{"type":"CustomControl","scope":"#\\/properties\\/firstAssessmentRejectedNote","options":{"readonly":true},"rule":{"effect":"SHOW","condition":{"scope":"#\\/properties\\/firstAssessment","schema":{"const":"Afgekeurd"}}}},{"type":"CustomControl","scope":"#\\/properties\\/firstAssessmentInternalNote","options":{"readonly":true}}]}]}' WHERE id = '71f71916-c0ed-45bc-8186-1b4f5dfb69e8';
UPDATE public.subsidy_stage_uis SET input_ui = E'{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\\/properties\\/implementationCoordinatorAssessment","options":{"format":"radio"}}]}]},{"type":"Group","label":"Toelichting","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\\/properties\\/coordinatorImplementationReasonForRejection","options":{"tip":"Toelichting:  Deze notitie wordt opgenomen binnen de brief aan de aanvrager."},"rule":{"effect":"SHOW","condition":{"scope":"#\\/properties\\/implementationCoordinatorAssessment","schema":{"const":"Afgekeurd"}}}},{"type":"CustomControl","scope":"#\\/properties\\/coordinatorImplementationApprovalNote","options":{"tip":"Toelichting:  Deze notitie wordt opgenomen binnen de brief aan de aanvrager."},"rule":{"effect":"SHOW","condition":{"scope":"#\\/properties\\/implementationCoordinatorAssessment","schema":{"const":"Goedgekeurd"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\\/properties\\/coordinatorImplementationInternalNote","options":[]}]}]}]}', view_ui = E'{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"CustomControl","scope":"#\\/properties\\/implementationCoordinatorAssessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\\/properties\\/implementationCoordinatorInternalNote","options":{"readonly":true}}]}' WHERE id = 'c51302f6-e131-45ff-8d4b-f4ff4a39b52f';
UPDATE public.subsidy_stage_uis SET input_ui = E'{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Checklist","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\\/properties\\/internalAssessmentChecklist","options":{"format":"checkbox-group"}}]}]},{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\\/properties\\/internalAssessment","options":{"format":"radio"}}]}]},{"type":"Group","label":"Toelichting","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\\/properties\\/internalAssessmentInternalNote","options":[]}]}]}]}', view_ui = E'{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"CustomControl","scope":"#\\/properties\\/internalAssessmentChecklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#\\/properties\\/internalAssessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\\/properties\\/internalAssessmentInternalNote","options":{"readonly":true}}]}' WHERE id = 'e819df05-03b7-4f37-b315-7f62339fd067';

--
-- Deleting data from table "public"."subsidy_stages"
--
DELETE FROM public.subsidy_stages WHERE id = '09718b39-800d-366c-b91f-e6d49e2ce2eb';
DELETE FROM public.subsidy_stages WHERE id = '14da837e-49bd-35bd-ad0b-395f9994cd08';
DELETE FROM public.subsidy_stages WHERE id = '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c';
DELETE FROM public.subsidy_stages WHERE id = '721c1c28-e674-415f-b1c3-872a631ed045';
DELETE FROM public.subsidy_stages WHERE id = 'c4bffbaf-8c47-34c1-823b-1afc7ecc717b';

--
-- Deleting data from table "public"."subsidy_versions"
--
DELETE FROM public.subsidy_versions WHERE id = '907bb399-0d19-4e1a-ac75-25a864df27c6';
DELETE FROM public.subsidy_versions WHERE id = '9b9c3f26-8309-4a7e-bd43-998166d2fd97';
DELETE FROM public.subsidy_versions WHERE id = 'f5eeeca5-1f23-4a19-a31c-55e6b958f3ec';

--
-- Creating constraints for "public"."fields"
--
ALTER TABLE public.fields
    ADD constraint fields_subsidy_stage_id_foreign foreign key (subsidy_stage_id) references subsidy_stages (id);

--
-- Creating constraints for "public"."subsidy_stage_transition_messages"
--
ALTER TABLE public.subsidy_stage_transition_messages
    ADD constraint subsidy_stage_transition_messages_subsidy_stage_transition_id_f foreign key (subsidy_stage_transition_id) references subsidy_stage_transitions (id);

--
-- Creating constraints for "public"."subsidy_stage_uis"
--
ALTER TABLE public.subsidy_stage_uis
    ADD constraint form_uis_form_id_foreign foreign key (subsidy_stage_id) references subsidy_stages (id);

--
-- Creating constraints for "public"."subsidy_stages"
--
ALTER TABLE public.subsidy_stages
    ADD constraint subsidy_stages_subsidy_version_id_foreign foreign key (subsidy_version_id) references subsidy_versions (id);

--
-- Creating constraints for "public"."subsidy_versions"
--
ALTER TABLE public.subsidy_versions
    ADD constraint subsidy_versions_subsidy_id_foreign foreign key (subsidy_id) references subsidies (id);

--
-- Creating constraints for "public"."answers"
--
ALTER TABLE public.answers
    ADD constraint answers_field_id_foreign foreign key (field_id) references fields (id);

--
-- Creating constraints for "public"."subsidy_stage_hash_fields"
--
ALTER TABLE public.subsidy_stage_hash_fields
    ADD constraint form_hash_fields_field_id_foreign foreign key (field_id) references fields (id);

--
-- Creating constraints for "public"."application_stages"
--
ALTER TABLE public.application_stages
    ADD constraint application_stages_subsidy_stage_id_foreign foreign key (subsidy_stage_id) references subsidy_stages (id);

--
-- Creating constraints for "public"."subsidy_stage_hashes"
--
ALTER TABLE public.subsidy_stage_hashes
    ADD constraint form_hashes_form_id_foreign foreign key (subsidy_stage_id) references subsidy_stages (id);

--
-- Creating constraints for "public"."subsidy_stage_transitions"
--
ALTER TABLE public.subsidy_stage_transitions
    ADD constraint subsidy_stage_transitions_current_subsidy_stage_id_foreign foreign key (current_subsidy_stage_id) references subsidy_stages (id);

--
-- Creating constraints for "public"."applications"
--
ALTER TABLE public.applications
    ADD constraint applications_subsidy_version_id_foreign foreign key (subsidy_version_id) references subsidy_versions (id);
