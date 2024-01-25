update public.subsidies
set reference_prefix = 'BTV'
where id = '00f26400-7232-475f-922c-6b569b7e421a';

update public.subsidy_versions
set contact_mail_address = 'btv@minvws.nl'
where id = '907bb399-0d19-4e1a-ac75-25a864df27c6';

update public.subsidy_stages
set title = 'Interne controle'
where id = 'e456e790-1919-4a2b-b3d5-337d0053abe3';

delete
from public.answers
where field_id = (select id
                  from public.fields
                  where code = 'bankStatement' and subsidy_stage_id = '721c1c28-e674-415f-b1c3-872a631ed045');

delete from public.fields where code = 'bankStatement' and subsidy_stage_id = '721c1c28-e674-415f-b1c3-872a631ed045';

delete
from public.answers
where field_id = (select id
                  from public.fields
                  where code = 'permissionToProcessPersonalData' and subsidy_stage_id = '721c1c28-e674-415f-b1c3-872a631ed045');

delete from public.fields where code = 'permissionToProcessPersonalData' and subsidy_stage_id = '721c1c28-e674-415f-b1c3-872a631ed045';

update public.fields
set params = '{"options":["Uittreksel van het BRP is opgestuurd","De aanvrager is een ingezetene (> 4 maanden) in Nederland","De aanvrager is ouder dan 18 jaar","De verklaring van de arts over het behandeltraject is opgestuurd","De verklaring van de arts over het behandeltraject is minder dan 2 maanden oud","De verklaring van de arts over het behandeltraject is ondertekend en voorzien van een naamstempel","Het opgegeven BIG-nummer komt overeen met het BIG-register","De operatie heeft nog niet plaatsgevonden","De aanvrager heeft genderdysforie","De aanvrager heeft minimaal een jaar voor de aanvraag hormoonbehandeling ondergaan, of is hiermee vanwege medische redenen gestopt of kon deze om medische redenen niet ondergaan","De verklaring van de arts met de vermelding van de type behandeling is opgestuurd","De verklaring van de arts met de vermelding van de type behandeling is ondertekend en voorzien van een naamstempel","De type behandeling voldoet aan de voorwaarden conform de subsidieregeling"]}'
where code = 'firstAssessmentChecklist'
  and subsidy_stage_id = '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c';

update public.fields
set params = '{"options":["Eens met de eerste beoordeling","Oneens met de eerste beoordeling"],"default":null}'
where code = 'internalAssessment'
  and subsidy_stage_id = 'e456e790-1919-4a2b-b3d5-337d0053abe3';

insert into public.subsidy_stage_hashes ("id", "subsidy_stage_id", "name", "description", "created_at", "updated_at")
values ('70609201-1301-455c-942b-654236221970', '721c1c28-e674-415f-b1c3-872a631ed045', 'Bank account',
        'Bank account duplicate reporting', 'now()', 'now()');

insert into public.subsidy_stage_hash_fields ("subsidy_stage_hash_id", "field_id")
values ('70609201-1301-455c-942b-654236221970', (select id
                                                 from public.fields
                                                 where "subsidy_stage_id" = '721c1c28-e674-415f-b1c3-872a631ed045'
                                                   and "code" = 'bankAccountNumber'
                                                   and "title" = 'IBAN' limit 1)
    );

UPDATE public.fields
SET params = '{"maxItems": 20, "minItems": 1, "mimeTypes": ["image/jpeg", "image/png", "application/pdf"], "maxFileSize": 20971520}'
WHERE code = 'extractPopulationRegisterDocument'
  AND subsidy_stage_id = '721c1c28-e674-415f-b1c3-872a631ed045';

UPDATE public.fields
SET params = '{"maxItems": 20, "minItems": 1, "mimeTypes": ["image/jpeg", "image/png", "application/pdf"], "maxFileSize": 20971520}'
WHERE code = 'proofOfMedicalTreatmentDocument'
  AND subsidy_stage_id = '721c1c28-e674-415f-b1c3-872a631ed045';

UPDATE public.fields
SET params = '{"maxItems": 20, "minItems": 1, "mimeTypes": ["image/jpeg", "image/png", "application/pdf"], "maxFileSize": 20971520}'
WHERE code = 'proofOfTypeOfMedicalTreatmentDocument'
  AND subsidy_stage_id = '721c1c28-e674-415f-b1c3-872a631ed045';

update public.subsidy_stage_uis
set input_ui = '{"type":"CustomPageNavigationControl","elements":[{"type":"CustomPageControl","label":"start","elements":[{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"FormHtml","options":{"html":"<p class=\"warning\">\n    <span>Waarschuwing:<\/span>\n    Het invullen van een aanvraag kost ongeveer 10 minuten. U kunt uw aanvraag tussentijds opslaan. Zorg ervoor dat u\n    alle gevraagde documenten digitaal bij de hand heeft. Dit kan bijvoorbeeld een scan, schermafdruk of foto vanaf uw\n    mobiele telefoon zijn. Laat uw aanvraag niet langer dan noodzakelijk open staan op uw computer.\n<\/p>\n\n<h2>Introductie<\/h2>\n<p>\n    Bent u een transvrouw met genderdysforie? En bevindt u zich in een medisch transitietraject en\n    overweegt u een borstvergroting? U kunt dan in aanmerking komen voor een subsidie via de\n    subsidieregeling Borstprothesen transvrouwen. Met dit formulier vraagt u een subsidie aan voor een\n    plastisch-chirurgische borstconstructie. Deze behandeling moet bewezen effectief zijn.\n<\/p>\n<h2>Benodigde bestanden<\/h2>\n<p>Om in aanmerking te komen voor de subsidie, worden een aantal documenten aan u gevraagd:<\/p>\n<p><\/p>\n<ol>\n    <li>Uittreksel Basisregistratie Personen;<\/li>\n    <li>\n        Medische verklaring van uw BIG-geregistreerde arts over de behandeling die u tot nu toe heeft\n        gevolgd. Download het\n        <a\n            title=\"Medische verklaring behandeltraject format\"\n            href=\"https:\/\/www.dus-i.nl\/subsidies\/borstprothesen-transvrouwen\/documenten\/publicaties\/2019\/01\/14\/verklaring-behandelend-arts-borstprothesen-transvrouwen\"\n            target=\"_blank\"\n            rel=\"nofollow noopener external\"\n        >Medische verklaring behandeltraject format<\/a\n        >.\n    <\/li>\n    <li>\n        Medische verklaring van een BIG-geregistreerde arts over de behandeling (operatie) die zal\n        worden uitgevoerd. Download het\n        <a\n            title=\"Medische verklaring van het type behandeling format\"\n            href=\"https:\/\/www.dus-i.nl\/subsidies\/borstprothesen-transvrouwen\/documenten\/publicaties\/2021\/08\/05\/medische-verklaring-van-het-type-behandeling-borstprothesen-transvrouwen\"\n            target=\"_blank\"\n            rel=\"nofollow noopener external\"\n        >Medische verklaring van het type behandeling format<\/a\n        >.\n    <\/li>\n<\/ol>\n<p>In de derde stap van het aanvraagformulier kunt u deze uploaden.<\/p>\n<p>Laat uw aanvraag niet langer dan noodzakelijk open staan op uw computer.<\/p>\n<h2>Aanvraag starten<\/h2>\n"}}]}],"options":{"required":[],"allOf":[]}},{"type":"CustomPageControl","label":"Persoonsgegevens toevoegen","elements":[{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"Group","elements":[{"type":"FormNotification","options":{"displayAs":"explanation","message":"U moet ouder zijn dan 18 jaar (artikel 4 van de regeling)."}}]},{"type":"Group","label":"Persoonlijke informatie","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstName","label":"Voornaam","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/infix","label":"Tussenvoegsel","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/lastName","label":"Achternaam","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/dateOfBirth","label":"Geboortedatum","options":{"placeholder":""}}]}]},{"type":"Group","label":"Adres","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/street","label":"Straatnaam","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/houseNumber","label":"Huisnummer","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/houseNumberSuffix","label":"Huisnummer toevoeging","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/postalCode","label":"Postcode","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/city","label":"Plaatsnaam","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/country","label":"Land","options":{"format":"select","placeholder":"Selecteer een land"}}]}]},{"type":"Group","label":"Contact","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/phoneNumber","label":"Telefoonnummer","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/email","label":"E-mailadres","options":{"placeholder":"","tip":"U wordt via dit e-mailadres ge\u00efnformeerd over de status van uw aanvraag. Geef daarom alleen uw eigen e-mailadres door.","validation":["onBlur"]}}]}]},{"type":"Group","label":"Bank","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/bankAccountHolder","label":"Naam rekeninghouder","options":{"placeholder":"","validation":["onBlur"]}},{"type":"CustomControl","scope":"#\/properties\/bankAccountNumber","label":"IBAN","options":{"placeholder":"","tip":"Staat u onder bewind? Vermeld dan het IBAN van uw beheerrekening.","validation":["onValid"]}}]}]}]}],"options":{"required":["firstName","lastName","street","dateOfBirth","houseNumber","postalCode","city","country","phoneNumber","email","bankAccountHolder","bankAccountNumber"],"allOf":[]}},{"type":"CustomPageControl","label":"Documenten toevoegen","elements":[{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"Group","label":"Uittreksel bevolkingsregister","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/extractPopulationRegisterDocument","label":"Uittreksel bevolkingsregister","options":{"accept":"image\/jpeg,image\/png,.pdf","maxFileSize":20971520,"minItems":1,"maxItems":20,"tip":"U kunt een uittreksel uit het bevolkingsregister (de Basisregistratie personen) opvragen bij de gemeente waar u staat ingeschreven. Dit document bevat uw naam, geboortedatum en adres. Toegestane bestandstypen: pdf, jpg, jpeg, png, Maximale bestandsgrootte: 20 MB."}}]}]},{"type":"Group","label":"Medische verklaring behandeltraject","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/proofOfMedicalTreatmentDocument","label":"Verklaring behandeltraject","options":{"accept":"image\/jpeg,image\/png,.pdf","maxFileSize":20971520,"minItems":1,"maxItems":20,"tip":"De <a title=\"medische verklaring over het behandeltraject\" href=\"https://www.dus-i.nl/documenten/publicaties/2019/01/14/verklaring-behandelend-arts-borstprothesen-transvrouwen\" target=\"_blank\" rel=\"noopener\" class=\"external\">medische verklaring over het behandeltraject<\/a> dat u tot nu toe heeft gevolgd moet zijn ingevuld door de BIG-geregistreerde arts waar u in behandeling bent. Dit kan een huisarts of medisch specialist zijn die de hormonen voorschrijft en de behandeling begeleidt. De verklaring mag niet ouder zijn dan twee maanden. Toegestane bestandstypen: pdf, jpg, jpeg, png, Maximale bestandsgrootte: 20 MB."}}]}]},{"type":"Group","label":"Medische verklaring van het type behandeling","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/proofOfTypeOfMedicalTreatmentDocument","label":"Verklaring type behandeling","options":{"accept":"image\/jpeg,image\/png,.pdf","maxFileSize":20971520,"minItems":1,"maxItems":20,"tip":"De <a title=\"medische verklaring van het type behandeling\" href=\"https://www.dus-i.nl/subsidies/borstprothesen-transvrouwen/documenten/publicaties/2021/08/05/medische-verklaring-van-het-type-behandeling-borstprothesen-transvrouwen\" target=\"_blank\" rel=\"noopener\" class=\"external\">medische verklaring van het type behandeling<\/a> (operatie) dat zal worden uitgevoerd moet zijn ingevuld en ondertekend door een BIG-geregistreerde arts. Toegestane bestandstypen: pdf, jpg, jpeg, png, Maximale bestandsgrootte: 20 MB."}}]}]}]}],"options":{"required":["extractPopulationRegisterDocument","proofOfMedicalTreatmentDocument","proofOfTypeOfMedicalTreatmentDocument"],"allOf":[]}},{"type":"CustomPageControl","label":"Controleren en ondertekenen","elements":[{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"Group","label":"Controleren","elements":[{"type":"FormResultsTable","label":"Uw gegevens","options":{"fields":{"Naam":"{firstName} {infix} {lastName}","Adres":"{street} {houseNumber}{houseNumberSuffix} {postalCode} {city}","Telefoon":"{phoneNumber}","E-mailadres":"{email}","Geboortedatum":"{dateOfBirth}"}}}]},{"type":"Group","label":"Ondertekenen","elements":[{"type":"CustomControl","scope":"#\/properties\/truthfullyCompleted","label":"Waarheidsverklaring","options":{"description":"Ik verklaar het formulier naar waarheid te hebben ingevuld."}}]}]}],"options":{"required":["truthfullyCompleted"],"allOf":[]}}]}',
    view_ui  = '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"FormGroupControl","label":"Persoonlijke informatie","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Voornaam":"{firstName}","Tussenvoegsel":"{infix}","Achternaam":"{lastName}","Geboortedatum":"{dateOfBirth}"}}}]},{"type":"FormGroupControl","label":"Adres","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Land":"{country}","Straatnaam":"{street}","Huisnummer":"{houseNumber}","Huisnummer toevoeging":"{houseNumberSuffix}","Postcode":"{postalCode}","Plaatsnaam":"{city}"}}}]},{"type":"FormGroupControl","label":"Contact","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Telefoonnummer":"{phoneNumber}","E-mailadres":"{email}"}}}]},{"type":"FormGroupControl","label":"Bank","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"IBAN":"{bankAccountNumber}","Naam rekeninghouder":"{bankAccountHolder}"}}}]},{"type":"FormGroupControl","label":"Bestanden","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Uitreksel bevolkingsregister":"{extractPopulationRegisterDocument}","Verklaring behandeltraject":"{proofOfMedicalTreatmentDocument}","Verklaring type behandeling":"{proofOfTypeOfMedicalTreatmentDocument}"}}}]}]}'
where id = '72475863-7987-4375-94d7-21e04ff6552b';

update public.subsidy_stage_uis
set input_ui = '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Beoordeling","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentChecklist","options":{"format":"checkbox-group"}}]}]},{"type":"Group","label":"Uitkering","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/amount","options":{"format":"radio"}}]}]},{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessment","options":{"format":"radio"}}]}]},{"type":"Group","label":"Toelichting","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRequestedComplementReason","options":{"format":"radio"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRequestedComplementNote","options":{"format":"textarea","tip":"Deze notitie wordt opgenomen binnen de brief aan de aanvrager."},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRejectedNote","options":{"format":"textarea","tip":"Deze notitie wordt opgenomen binnen de brief aan de aanvrager."},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Afgekeurd"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentInternalNote","options":{"format":"textarea"}}]}]}]}',
    view_ui  = '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"FormGroupControl","label":"Persoonsgegevens","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentChecklist","options":{"readonly":true,"format":"checkbox-group"}}]},{"type":"FormGroupControl","label":"Uitkering","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/amount","options":{"readonly":true}}]},{"type":"FormGroupControl","label":"Eerste beoordeling","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRequestedComplementReason","options":{"readonly":true,"format":"radio"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}},{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRequestedComplementNote","options":{"readonly":true,"format":"textarea"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}},{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRejectedNote","options":{"readonly":true,"format":"textarea"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Afgekeurd"}}}},{"type":"CustomControl","scope":"#\/properties\/firstAssessmentInternalNote","options":{"readonly":true,"format":"textarea"}}]}]}'
where id = 'db1076a1-42f3-4c90-b1bf-57d1db025f2e';

update public.subsidy_stage_uis
set input_ui = '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessorMotivatedValid","options":{"format":"checkbox"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/secondAssessment","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/secondAssessmentInternalNote","options":{"format":"textarea"}}]}]}]}',
    view_ui  = '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessorMotivatedValid","options":{"readonly":true}},{"type":"CustomControl","scope":"#\/properties\/secondAssessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/secondAssessmentInternalNote","options":{"readonly":true,"format":"textarea"}}]}'
where id = '787c8ef4-bfcd-4bd4-aec1-dec02139c897';

update public.subsidy_stage_uis
set input_ui = '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Checklist","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/internalAssessmentChecklist","options":{"format":"checkbox-group"}}]}]},{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/internalAssessment","options":{"format":"radio"}}]}]},{"type":"Group","label":"Toelichting","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/internalAssessmentReasonForRejection","options":{"format":"textarea","tip":"Deze notitie wordt opgenomen binnen de brief aan de aanvrager."},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/internalAssessment","schema":{"const":"Oneens met de eerste beoordeling"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/internalAssessmentInternalNote","options":{"format":"textarea"}}]}]}]}',
    view_ui  = '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"CustomControl","scope":"#\/properties\/internalAssessmentChecklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#\/properties\/internalAssessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/internalAssessmentInternalNote","options":{"readonly":true,"format":"textarea"}},{"type":"CustomControl","scope":"#\/properties\/internalAssessmentReasonForRejection","options":{"readonly":true,"format":"textarea"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/internalAssessment","schema":{"const":"Oneens met de eerste beoordeling"}}}}]}'
where id = 'a6080627-0ea9-436e-bbba-c454bd3809fd';

update public.subsidy_stage_transitions
set description = 'Interne beoording oneens met eerste beoordeling',
    target_application_status = null,
    target_subsidy_stage_id = '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c',
    current_subsidy_stage_id = 'e456e790-1919-4a2b-b3d5-337d0053abe3',
    condition = '{"type":"comparison","stage":4,"fieldCode":"internalAssessment","operator":"===","value":"Oneens met de eerste beoordeling"}',
    send_message = false,
    assign_to_previous_assessor = true
where id = '3a293e03-1de4-47bf-917b-841b7c0a1fff';

update public.subsidy_stage_transitions
set description = 'Interne beoordeling eens met eerste beoordeling',
    current_subsidy_stage_id = 'e456e790-1919-4a2b-b3d5-337d0053abe3',
    target_application_status = 'approved',
    condition = '{"type":"comparison","stage":4,"fieldCode":"internalAssessment","operator":"===","value":"Eens met de eerste beoordeling"}'
where id = '5b876216-ba37-4b13-aa99-e311db027d6b';

delete from public.subsidy_stage_transition_messages
where id = '7476a2bd-15eb-4ab8-be8e-c9f3dd07f9b7';

UPDATE public.subsidy_stage_transition_messages
SET
    content_html                = e'{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de \'Regeling {$content->subsidyTitle}\' met referentienummer: {$content->reference}.
    </p>

    <p>Uw aanvraag bevat helaas nog onvoldoende gegevens om verder in behandeling te nemen. Met deze brief verzoek ik u om uw aanvraag aan te vullen.</p>

    <h2>Wat moet u aanvullen?</h2>
    <p>
        Ik verzoek u om uw aanvraag aan te vullen met:<br/>
        {$content->stage2->firstAssessmentRequestedComplementNote|breakLines}
    </p>
    <p>&nbsp;</p>

    <h2>Termijn</h2>
    <p>
        Ik ontvang de informatie graag binnen 2 weken na dagtekening van deze brief, dus uiterlijk {$content->createdAt->addDays(14)|date:"d-m-Y"}.
    </p>
    <p>
        U kunt de ontbrekende informatie aan uw aanvraag toevoegen door het door u ingevulde aanvraagformulier te heropenen in Mijn DUS-I.
    </p>
    <p>
        Als de gevraagde gegevens niet binnen 2 weken door mij zijn ontvangen, of onvoldoende zijn voor verdere beoordeling, dan kan uw aanvraag niet verder worden behandeld.
    </p>
    <p>&nbsp;</p>
{/block}
', content_pdf = e'{layout \'letter_layout.latte\'}

{block concern}
    Betreft: Verzoek om aanvulling aanvraag \'Regeling {$content->subsidyTitle}\'
{/block}

{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de \'Regeling {$content->subsidyTitle}\' met referentienummer: {$content->reference}.
    </p>

    <p>Uw aanvraag bevat helaas nog onvoldoende gegevens om verder in behandeling te nemen. Met deze brief verzoek ik u om uw aanvraag aan te vullen.</p>

    <h2>Wat moet u aanvullen?</h2>
    <p>
        Ik verzoek u om uw aanvraag aan te vullen met:<br/>
        {$content->stage2->firstAssessmentRequestedComplementNote|breakLines}
    </p>
    <p>&nbsp;</p>

    <h2>Termijn</h2>
    <p>
        Ik ontvang de informatie graag binnen 2 weken na dagtekening van deze brief, dus uiterlijk {$content->createdAt->addDays(14)|date:"d-m-Y"}.
    </p>
    <p>
        U kunt de ontbrekende informatie aan uw aanvraag toevoegen door het door u ingevulde aanvraagformulier te heropenen in Mijn DUS-I.
    </p>
    <p>
        Als de gevraagde gegevens niet binnen 2 weken door mij zijn ontvangen, of onvoldoende zijn voor verdere beoordeling, dan kan uw aanvraag niet verder worden behandeld.
    </p>
    <p>&nbsp;</p>
{/block}

{block sidebar}
    {include parent}
{/block}

{block objectionFooter}{/block}
', created_at = '2024-01-25 11:06:10', updated_at = null
WHERE id = 'cffe3600-77a9-43b2-9882-7b7f56c4d0ad';

UPDATE public.subsidy_stage_transition_messages
SET content_html                = e'{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>
    <p>&nbsp;</p>

    <h2>Besluit</h2>
    <p>Uw aanvraag moet ik helaas afwijzen. De reden(en) licht ik hieronder toe.</p>
    <p>&nbsp;</p>


    <h2>Motivering bij het besluit</h2>

    <p>U heeft een aanvraag voor de subsidieregeling borstprothesen transvrouwen ingediend, die verstrekt wordt op grond
        van de subsidieregeling borstprothesen transvrouwen (hierna: de Beleidsregel). Het doel van de Beleidsregel is
        dat man-vrouw transgenders met genderdysforie, die zich in een medisch transitietraject bevinden, door een
        vergroting van de borsten een vrouwelijk(er) profiel kunnen krijgen.</p>

    <p>
        In artikel 4 van de Beleidsregel is opgenomen dat een subsidie uitsluitend wordt toegekend aan transvrouwen die:<br/>
        <ul>
            <li>Ingezetene zijn in Nederland;</li>
            <li>Ouder zijn dan 18 jaar;</li>
            <li>Op voorschrift van een BIG-geregistreerde arts:<br/>
                <ul>
                    <li>minimaal een jaar voorafgaand aan de subsidieaanvraag genderbevestigende behandeling hebben
                        ondergaan;
                        of
                    </li>
                    <li>op medische gronden geen genderbevestigende hormoonbehandelingen kunnen ondergaan; of</li>
                    <li>om medische redenen binnen de tijdspanne van een jaar zijn gestopt met de hoormoontherapie;</li>
                </ul>
            </li>
            <li>De operatie nog niet ondergaan hebben;</li>
            <li>Geen aanspraak kunnen maken op vergoeding van het operatief plaatsen van borstprothesen op grond van artikel
                2.1, onderdeel c, van de Regeling zorgverzekering.
            </li>
        </ul>
    </p>

    {if $content->stage2->firstAssessmentRejectedNote}
        <p>
            Uw aanvraag is getoetst aan de bovengenoemde voorwaarden en wordt afgewezen vanwege de volgende
            reden(en):
        </p>
        <p>{$content->stage2->firstAssessmentRejectedNote|breakLines}</p>
        <p>&nbsp;</p>
    {/if}
{/block}
', content_pdf = e'{layout \'letter_layout.latte\'}

{block concern}
    Betreft: Afwijzing aanvraag {$content->subsidyTitle}
{/block}

{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>
    <p>&nbsp;</p>

    <h2>Besluit</h2>
    <p>Uw aanvraag moet ik helaas afwijzen. De reden(en) licht ik hieronder toe.</p>
    <p>&nbsp;</p>


    <h2>Motivering bij het besluit</h2>

    <p>U heeft een aanvraag voor de subsidieregeling borstprothesen transvrouwen ingediend, die verstrekt wordt op grond
        van de subsidieregeling borstprothesen transvrouwen (hierna: de Beleidsregel). Het doel van de Beleidsregel is
        dat man-vrouw transgenders met genderdysforie, die zich in een medisch transitietraject bevinden, door een
        vergroting van de borsten een vrouwelijk(er) profiel kunnen krijgen.</p>

    <p>
        In artikel 4 van de Beleidsregel is opgenomen dat een subsidie uitsluitend wordt toegekend aan transvrouwen die:<br/>
        <ul>
            <li>Ingezetene zijn in Nederland;</li>
            <li>Ouder zijn dan 18 jaar;</li>
            <li>Op voorschrift van een BIG-geregistreerde arts:<br/>
                <ul>
                    <li>minimaal een jaar voorafgaand aan de subsidieaanvraag genderbevestigende behandeling hebben
                        ondergaan;
                        of
                    </li>
                    <li>op medische gronden geen genderbevestigende hormoonbehandelingen kunnen ondergaan; of</li>
                    <li>om medische redenen binnen de tijdspanne van een jaar zijn gestopt met de hoormoontherapie;</li>
                </ul>
            </li>
            <li>De operatie nog niet ondergaan hebben;</li>
            <li>Geen aanspraak kunnen maken op vergoeding van het operatief plaatsen van borstprothesen op grond van artikel
                2.1, onderdeel c, van de Regeling zorgverzekering.
            </li>
        </ul>
    </p>

    {if $content->stage2->firstAssessmentRejectedNote}
        <p>
            Uw aanvraag is getoetst aan de bovengenoemde voorwaarden en wordt afgewezen vanwege de volgende
            reden(en):
        </p>
        <p>{$content->stage2->firstAssessmentRejectedNote|breakLines}</p>
        <p>&nbsp;</p>
    {/if}
{/block}

{block sidebar}
    {include parent}
{/block}
', updated_at = now()
WHERE id = 'c3b32e69-e093-4f0f-9318-7cc771114f2d';

UPDATE public.subsidy_stage_transition_messages
SET content_html                = e'{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>
    <p>&nbsp;</p>

    <h2>Besluit</h2>
    <p>
        Uw subsidieaanvraag borstprothesen transvrouwen is toegewezen. De hoogte van de subsidie is
        {$content->stage2->amount}.
    </p>
    <p>
        De subsidie is gebaseerd op artikel 4 van de subsidieregeling borstprothesen transvrouwen. De
        subsidieregeling heeft als doel dat man-vrouw transgenders met genderdysforie, die zich in een medisch
        transitietraject bevinden, door een vergroting van de borsten een vrouwelijk(er) profiel kunnen krijgen.
    </p>
    <p>
       Volgens artikel 8 van de subsidieregeling ontvangt u een voorschot van 100% van het subsidiebedrag. De
       operatie dient in beginsel binnen een jaar na de aanvraag van de subsidie verricht te zijn. Als dit door
       omstandigheden niet mogelijk is gebleken, kan ontheffing of vrijstelling van deze termijn verleend worden.
    </p>
    <p>&nbsp;</p>

    <h2>Motivering bij besluit</h2>
    <p>
        Op grond van uw aanvraag en de verklaring stel ik vast dat u voldoet aan de voorwaarden van de
        subsidie borstprothesen transvrouwen.
    </p>

    <h2>Waar moet u aan voldoen?</h2>
    <p>U moet voldoen aan de verplichtingen in de wet- en regelgeving die van toepassing zijn op de subsidie en aan al
        hetgeen in deze beschikking is opgenomen.</p>

    <p>Wet- en regelgeving
        De volgende regelgeving is in ieder geval van toepassing op de subsidie:<br/>
        <ul>
            <li>Kaderwet VWS-subsidies</li>
            <li>Algemene wet bestuursrecht, in het bijzonder titel 4.2 Subsidies;</li>
            <li>Subsidieregeling borstprothesen transvrouwen</li>
        </ul>
    </p>

    <p>De regelgeving kunt u raadplegen via <a href="https://wetten.overheid.nl" target="_blank">wetten.overheid.nl</a>.</p>

    <p>
        U bent zelf verantwoordelijk voor de naleving hiervan en de eventuele gevolgen
        bij niet-naleving.
    </p>

    <p>Zonder volledig te zijn breng ik in het bijzonder de volgende bepalingen uit de
        wet- en regelgeving onder uw aandacht.</p>

    <p>
        <b>Operatief plaatsen van borstprothesen</b><br/>
        Deze subsidie wordt verstrekt ten behoeve van het operatief plaatsen van borstprothesen en de medisch
        noodzakelijke kosten die samenhangen met deze operatie. De subsidie mag niet worden gebruikt voor andere
        (operatieve) ingrepen ten behoeve van een borstvergroting.
    </p>

    <p>
        <b>Meldingsplicht</b><br/>
        Indien zich na indiening van de aanvraag omstandigheden voordoen die van belang kunnen zijn voor de beslissing
        tot vaststelling van de subsidie, doet u daarvan zo spoedig mogelijk schriftelijk mededeling aan de Minister zo
        mogelijk onder overlegging van de relevante stukken.
    </p>

    <p>
        Uw melding, voorzien van toelichting en relevante stukken, doet u schriftelijk bij de Dienst Uitvoering Subsidies
        aan Instellingen (DUS-I) onder vermelding van het zaaknummer {$content->reference}. Als u twijfelt of u iets moet
        melden, verzoek ik u contact op te nemen met uw contactpersoon.
    </p>

    <h2>Wat als u zich niet aan de voorschriften houdt?</h2>
    <p>
        Het niet voldoen aan de verplichtingen die aan de subsidie verbonden zijn of het
        niet (geheel) verrichten van de activiteiten kan tot gevolg hebben dat ik de
        subsidie geheel of gedeeltelijk terugvorder.

    </p>
    <p>
        Ik wijs u er verder op dat een registratie van (ernstige) onregelmatigheden bij
        subsidies wordt bijgehouden met het oog op het tegengaan van misbruik van
        subsidie.
    </p>

    <h2>Wanneer ontvangt u de financiële ondersteuning?</h2>
    <p>
        U ontvangt een voorschot van 100% van het subsidiebedrag.<br>
        Ik streef ernaar dit binnen 10 werkdagen naar u over te maken onder vermelding van het
        referentienummer {$content->reference}.
    </p>

    <h2>Wanneer wordt de subsidie vastgesteld?</h2>
    <p>
        Op basis van artikel 10 van de subsidieregeling neemt de minister binnen 22 weken na afloop van de datum waarop
        de operatie waarvoor de subsidie is verleend, moet zijn verricht, ambtshalve een besluit over de vaststelling
        van de subsidie. Er vindt een steekproefsgewijze controle plaats. Indien u voor deze steekproef geselecteerd
        wordt, zal er aan u gevraagd worden om een factuur van de operatie aan te leveren. Daarom is het advies om uw
        factuur goed te bewaren.
    </p>

{/block}
', content_pdf = e'{layout \'letter_layout.latte\'}

{block concern}
    Betreft: Verlening aanvraag {$content->subsidyTitle}
{/block}

{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>
    <p>&nbsp;</p>

    <h2>Besluit</h2>
    <p>
        Uw subsidieaanvraag borstprothesen transvrouwen is toegewezen. De hoogte van de subsidie is
        {$content->stage2->amount}.
    </p>
    <p>
        De subsidie is gebaseerd op artikel 4 van de subsidieregeling borstprothesen transvrouwen. De
        subsidieregeling heeft als doel dat man-vrouw transgenders met genderdysforie, die zich in een medisch
        transitietraject bevinden, door een vergroting van de borsten een vrouwelijk(er) profiel kunnen krijgen.
    </p>
    <p>
       Volgens artikel 8 van de subsidieregeling ontvangt u een voorschot van 100% van het subsidiebedrag. De
       operatie dient in beginsel binnen een jaar na de aanvraag van de subsidie verricht te zijn. Als dit door
       omstandigheden niet mogelijk is gebleken, kan ontheffing of vrijstelling van deze termijn verleend worden.
    </p>
    <p>&nbsp;</p>

    <h2>Motivering bij besluit</h2>
    <p>
        Op grond van uw aanvraag en de verklaring stel ik vast dat u voldoet aan de voorwaarden van de
        subsidie borstprothesen transvrouwen.
    </p>

    <h2>Waar moet u aan voldoen?</h2>
    <p>U moet voldoen aan de verplichtingen in de wet- en regelgeving die van toepassing zijn op de subsidie en aan al
        hetgeen in deze beschikking is opgenomen.</p>

    <p>Wet- en regelgeving
        De volgende regelgeving is in ieder geval van toepassing op de subsidie:<br/>
        <ul>
            <li>Kaderwet VWS-subsidies</li>
            <li>Algemene wet bestuursrecht, in het bijzonder titel 4.2 Subsidies;</li>
            <li>Subsidieregeling borstprothesen transvrouwen</li>
        </ul>
    </p>

    <p>De regelgeving kunt u raadplegen via <a href="https://wetten.overheid.nl" target="_blank">wetten.overheid.nl</a>.</p>

    <p>
        U bent zelf verantwoordelijk voor de naleving hiervan en de eventuele gevolgen
        bij niet-naleving.
    </p>

    <p>Zonder volledig te zijn breng ik in het bijzonder de volgende bepalingen uit de
        wet- en regelgeving onder uw aandacht.</p>

    <p>
        <b>Operatief plaatsen van borstprothesen</b><br/>
        Deze subsidie wordt verstrekt ten behoeve van het operatief plaatsen van borstprothesen en de medisch
        noodzakelijke kosten die samenhangen met deze operatie. De subsidie mag niet worden gebruikt voor andere
        (operatieve) ingrepen ten behoeve van een borstvergroting.
    </p>

    <p>
        <b>Meldingsplicht</b><br/>
        Indien zich na indiening van de aanvraag omstandigheden voordoen die van belang kunnen zijn voor de beslissing
        tot vaststelling van de subsidie, doet u daarvan zo spoedig mogelijk schriftelijk mededeling aan de Minister zo
        mogelijk onder overlegging van de relevante stukken.
    </p>

    <p>
        Uw melding, voorzien van toelichting en relevante stukken, doet u schriftelijk bij de Dienst Uitvoering Subsidies
        aan Instellingen (DUS-I) onder vermelding van het zaaknummer {$content->reference}. Als u twijfelt of u iets moet
        melden, verzoek ik u contact op te nemen met uw contactpersoon.
    </p>

    <h2>Wat als u zich niet aan de voorschriften houdt?</h2>
    <p>
        Het niet voldoen aan de verplichtingen die aan de subsidie verbonden zijn of het
        niet (geheel) verrichten van de activiteiten kan tot gevolg hebben dat ik de
        subsidie geheel of gedeeltelijk terugvorder.

    </p>
    <p>
        Ik wijs u er verder op dat een registratie van (ernstige) onregelmatigheden bij
        subsidies wordt bijgehouden met het oog op het tegengaan van misbruik van
        subsidie.
    </p>

    <h2>Wanneer ontvangt u de financiële ondersteuning?</h2>
    <p>
        U ontvangt een voorschot van 100% van het subsidiebedrag.<br>
        Ik streef ernaar dit binnen 10 werkdagen naar u over te maken onder vermelding van het
        referentienummer {$content->reference}.
    </p>

    <h2>Wanneer wordt de subsidie vastgesteld?</h2>
    <p>
        Op basis van artikel 10 van de subsidieregeling neemt de minister binnen 22 weken na afloop van de datum waarop
        de operatie waarvoor de subsidie is verleend, moet zijn verricht, ambtshalve een besluit over de vaststelling
        van de subsidie. Er vindt een steekproefsgewijze controle plaats. Indien u voor deze steekproef geselecteerd
        wordt, zal er aan u gevraagd worden om een factuur van de operatie aan te leveren. Daarom is het advies om uw
        factuur goed te bewaren.
    </p>

{/block}

{block sidebar}
    {include parent}
{/block}
', updated_at = now()
WHERE id = '1983fa28-cfc6-4c0f-9bc3-cba9e0909456';

