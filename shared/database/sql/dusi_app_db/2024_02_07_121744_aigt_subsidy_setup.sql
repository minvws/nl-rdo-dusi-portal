insert into public.subsidies ("id", "title", "reference_prefix", "code", "description", "valid_from", "valid_to")
values ('cb91d7d4-6261-4cd6-96e8-d09c86a670b7',
        'Opleidingsactiviteiten arts internationale gezondheid en tropengeneeskunde', 'AIGT', 'AIGT',
        'Voor artsen in opleiding tot Arts Internationale Gezondheid en Tropengeneeskunde brengt de eindstage van 6 maanden in het buitenland hoge kosten met zich mee. De subsidie Opleidingsactiviteiten AIGT 2021-2026 compenseert deze kosten.',
        'now()', null);

insert into public.subsidy_versions ("id", "subsidy_id", "version", "status", "created_at", "subsidy_page_url",
                                "contact_mail_address", "mail_to_address_field_identifier",
                                "mail_to_name_field_identifier", "review_period")
values ('2aaac0da-d265-40bb-bde6-ac20d77e6bca', 'cb91d7d4-6261-4cd6-96e8-d09c86a670b7', 1, 'published', 'now()',
        'https://www.dus-i.nl/subsidies/opleidingsactiviteiten-arts-internationale-gezondheid-en-tropengeneeskunde',
        'dienstpostbus@minvws.nl', 'email', 'firstName;infix;lastName', 91);

insert into public.subsidy_stages ("id", "subsidy_version_id", "title", "subject_role", "stage")
values ('a0f9ed92-c553-42d9-aef6-707bdfadd2d1', '2aaac0da-d265-40bb-bde6-ac20d77e6bca', 'Aanvraag', 'applicant', 1);

insert into public.subsidy_stages ("id", "subsidy_version_id", "title", "subject_role", "assessor_user_role", "stage",
                              "internal_note_field_code")
values ('7075fcad-7d92-42f6-b46c-7733869019e0', '2aaac0da-d265-40bb-bde6-ac20d77e6bca', 'Eerste beoordeling',
        'assessor', 'assessor', 2, 'firstAssessmentInternalNote');

insert into public.subsidy_stages ("id", "subsidy_version_id", "title", "subject_role", "assessor_user_role", "stage",
                              "internal_note_field_code")
values ('0838f8a9-b2ff-4669-9d42-1c51a1134a34', '2aaac0da-d265-40bb-bde6-ac20d77e6bca', 'Interne controle', 'assessor',
        'internalAuditor', 3, 'interalAssessmentInternalNote');

insert into public.subsidy_stages ("id", "subsidy_version_id", "title", "subject_role", "assessor_user_role", "stage",
                              "internal_note_field_code")
values ('e5da8f2e-db87-45df-8967-ea3dceb2b207', '2aaac0da-d265-40bb-bde6-ac20d77e6bca',
        'Uitvoeringscoördinator controle', 'assessor', 'implementationCoordinator', 4,
        'implementationCoordinatorAssessmentInternalNote');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('778f2bae-fbc4-4eb7-b81c-47829d206987', 'a0f9ed92-c553-42d9-aef6-707bdfadd2d1', 'firstName', 'Voornaam', null,
        'text', '{"maxLength":null}', true, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('21fd192d-d79a-4cee-9abc-d5bded6eea2d', 'a0f9ed92-c553-42d9-aef6-707bdfadd2d1', 'infix', 'Tussenvoegsel', null,
        'text', '{"maxLength":null}', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('4f8c2433-ca52-4f52-86bb-ace29f81e5de', 'a0f9ed92-c553-42d9-aef6-707bdfadd2d1', 'lastName', 'Achternaam', null,
        'text', '{"maxLength":null}', true, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('13cb9161-49fa-4fff-9b92-e2b79e7c06fd', 'a0f9ed92-c553-42d9-aef6-707bdfadd2d1', 'street', 'Straat', null,
        'text', '{"maxLength":null}', true, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('598939d3-e7e2-47bc-87b2-34d6e2353541', 'a0f9ed92-c553-42d9-aef6-707bdfadd2d1', 'houseNumber', 'Huisnummer',
        null, 'text:numeric', '{"maxLength":null}', true, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('cf5ba4ad-b91d-4d91-bca4-7f3ea9f047a9', 'a0f9ed92-c553-42d9-aef6-707bdfadd2d1', 'houseNumberSuffix',
        'Huisnummer toevoeging', null, 'text', '{"maxLength":10}', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('92748fc5-c6ae-49a1-ac83-787fd66cbb4d', 'a0f9ed92-c553-42d9-aef6-707bdfadd2d1', 'postalCode', 'Postcode', null,
        'custom:postalcode', 'null', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('beda012e-a318-44fb-8d0e-01a0c93e05c0', 'a0f9ed92-c553-42d9-aef6-707bdfadd2d1', 'city', 'Plaats', null, 'text',
        '{"maxLength":100}', true, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('031d85df-1fb4-462b-b390-7d55c1ba505a', 'a0f9ed92-c553-42d9-aef6-707bdfadd2d1', 'country', 'Land', null,
        'select',
        '{"options":["Afghanistan","\u00c5land","Albani\u00eb","Algerije","Amerikaanse Maagdeneilanden","Amerikaans-Samoa","Andorra","Angola","Anguilla","Antarctica","Antigua en Barbuda","Argentini\u00eb","Armeni\u00eb","Aruba","Australi\u00eb","Azerbeidzjan","Bahama\u2019s","Bahrein","Bangladesh","Barbados","Belgi\u00eb","Belize","Benin","Bermuda","Bhutan","Bolivia","Bosni\u00eb en Herzegovina","Botswana","Bouveteiland","Brazili\u00eb","Britse Maagdeneilanden","Brits Indische Oceaanterritorium","Brunei","Bulgarije","Burkina Faso","Burundi","Cambodja","Canada","Centraal-Afrikaanse Republiek","Chili","China","Christmaseiland","Cocoseilanden","Colombia","Comoren","Congo-Brazzaville","Congo-Kinshasa","Cookeilanden","Costa Rica","Cuba","Cura\u00e7ao","Cyprus","Denemarken","Djibouti","Dominica","Dominicaanse Republiek","Duitsland","Ecuador","Egypte","El Salvador","Equatoriaal-Guinea","Eritrea","Estland","Ethiopi\u00eb","Faer\u00f6er","Falklandeilanden","Fiji","Filipijnen","Finland","Frankrijk","Franse Zuidelijke en Antarctische Gebieden","Frans-Guyana","Frans-Polynesi\u00eb","Gabon","Gambia","Georgi\u00eb","Ghana","Gibraltar","Grenada","Griekenland","Groenland","Guadeloupe","Guam","Guatemala","Guernsey","Guinee","Guinee-Bissau","Guyana","Ha\u00efti","Heard en McDonaldeilanden","Honduras","Hongarije","Hongkong","Ierland","IJsland","India","Indonesi\u00eb","Irak","Iran","Isra\u00ebl","Itali\u00eb","Ivoorkust","Jamaica","Japan","Jemen","Jersey","Jordani\u00eb","Kaaimaneilanden","Kaapverdi\u00eb","Kameroen","Kazachstan","Kenia","Kirgizi\u00eb","Kiribati","Kleine Pacifische eilanden van de Verenigde Staten","Koeweit","Kroati\u00eb","Laos","Lesotho","Letland","Libanon","Liberia","Libi\u00eb","Liechtenstein","Litouwen","Luxemburg","Macau","Madagaskar","Malawi","Maldiven","Maleisi\u00eb","Mali","Malta","Marokko","Marshalleilanden","Martinique","Mauritani\u00eb","Mauritius","Mayotte","Mexico","Micronesia","Moldavi\u00eb","Monaco","Mongoli\u00eb","Montenegro","Montserrat","Mozambique","Myanmar","Namibi\u00eb","Nauru","Nederland","Nepal","Nicaragua","Nieuw-Caledoni\u00eb","Nieuw-Zeeland","Niger","Nigeria","Niue","Noordelijke Marianen","Noord-Korea","Noord-Macedoni\u00eb","code Land","Noorwegen","Norfolk","Oeganda","Oekra\u00efne","Oezbekistan","Oman","Oostenrijk","Oost-Timor","Pakistan","Palau","Palestina","Panama","Papoea-Nieuw-Guinea","Paraguay","Peru","Pitcairneilanden","Polen","Portugal","Puerto Rico","Qatar","R\u00e9union","Roemeni\u00eb","Rusland","Rwanda","Saint-Barth\u00e9lemy","Saint Kitts en Nevis","Saint Lucia","Saint-Pierre en Miquelon","Saint Vincent en de Grenadines","Salomonseilanden","Samoa","San Marino","Saoedi-Arabi\u00eb","Sao Tom\u00e9 en Principe","Senegal","Servi\u00eb","Seychellen","Sierra Leone","Singapore","Sint-Helena, Ascension en Tristan da Cunha","Sint-Maarten","Sint Maarten","Sloveni\u00eb","Slowakije","Soedan","Somali\u00eb","Spanje","Spitsbergen en Jan Mayen","Sri Lanka","Suriname","Swaziland","Syri\u00eb","Tadzjikistan","Taiwan","Tanzania","Thailand","Togo","Tokelau","Tonga","Trinidad en Tobago","Tsjaad","Tsjechi\u00eb","Tunesi\u00eb","Turkije","Turkmenistan","Turks- en Caicoseilanden","Tuvalu","Uruguay","Vanuatu","Vaticaanstad","Venezuela","Verenigde Arabische Emiraten","Verenigde Staten","Verenigd Koninkrijk","Vietnam","Wallis en Futuna","Westelijke Sahara","Wit-Rusland","Zambia","Zimbabwe","Zuid-Afrika","Zuid-Georgia en de Zuidelijke Sandwicheilanden","Zuid-Korea","Zuid-Soedan","Zweden","Zwitserland"],"default":"Nederland"}',
        true, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('79a63fc7-fa0d-4c62-8d22-995fa957458e', 'a0f9ed92-c553-42d9-aef6-707bdfadd2d1', 'phoneNumber', 'Telefoonnummer',
        null, 'text:tel', '{"maxLength":20}', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('1bf582ea-967a-4030-be64-dfd4606a40e0', 'a0f9ed92-c553-42d9-aef6-707bdfadd2d1', 'email', 'E-mailadres', null,
        'text:email', '{"maxLength":300}', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('f3bd8ffe-ad63-4492-bb35-309b6e5c53cd', 'a0f9ed92-c553-42d9-aef6-707bdfadd2d1', 'bankAccountNumber', 'IBAN',
        null, 'custom:bankaccount', 'null', true, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('1e98d667-adc3-47ed-86d6-2ace21b047fc', 'a0f9ed92-c553-42d9-aef6-707bdfadd2d1', 'bankAccountHolder',
        'Naam rekeninghouder', null, 'text', '{"maxLength":50}', true, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('50f00737-7fab-4597-9640-86f77c816cc0', 'a0f9ed92-c553-42d9-aef6-707bdfadd2d1',
        'abroadCourseComponentStartDate', 'Start opleidingsonderdeel buitenland', null, 'date', 'null', true, null,
        'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('4080bf6a-e4cd-43c0-9aad-d8f38fb4c04a', 'a0f9ed92-c553-42d9-aef6-707bdfadd2d1', 'proofOfRegistrationDocument',
        'Bewijs van inschrijving', null, 'upload',
        '{"mimeTypes":["image\/jpeg","image\/png","application\/pdf"],"maxFileSize":20971520,"minItems":1,"maxItems":20}',
        true, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('eb51abbd-0152-44ed-87f3-9b57b5714094', 'a0f9ed92-c553-42d9-aef6-707bdfadd2d1',
        'educationalInstituteDeclarationDocument', 'Verklaring van opleidingsinstituut', null, 'upload',
        '{"mimeTypes":["image\/jpeg","image\/png","application\/pdf"],"maxFileSize":20971520,"minItems":1,"maxItems":20}',
        true, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('5f605ed9-e1f6-4bee-8831-38d7e7198c1c', 'a0f9ed92-c553-42d9-aef6-707bdfadd2d1', 'truthfullyCompleted', '', null,
        'checkbox', 'null', true, null, 'short', true);

insert into public.subsidy_stage_hashes ("id", "subsidy_stage_id", "name", "description", "created_at", "updated_at")
values ('c47536b4-b44a-4621-b677-f61ce34997d5', 'a0f9ed92-c553-42d9-aef6-707bdfadd2d1', 'Bank account',
        'Bank account duplicate reporting', '2024-01-23 12:47:44', '2024-01-23 12:47:44');

insert into public.subsidy_stage_hash_fields ("subsidy_stage_hash_id", "field_id")
values ('c47536b4-b44a-4621-b677-f61ce34997d5', (select id
                                                 from public.fields
                                                 where "subsidy_stage_id" = 'a0f9ed92-c553-42d9-aef6-707bdfadd2d1'
                                                   and "code" = 'bankAccountNumber'
                                                   and "title" = 'IBAN' limit 1)
    );

insert into public.subsidy_stage_uis ("id", "subsidy_stage_id", "version", "status", "input_ui", "view_ui")
values ('491a4071-c401-4041-97fd-39b8c3aa70c8', 'a0f9ed92-c553-42d9-aef6-707bdfadd2d1', 1, 'published',
        '{"type":"CustomPageNavigationControl","elements":[{"type":"CustomPageControl","label":"start","elements":[{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"FormHtml","options":{"html":"<p class=\"warning\">\n    <span>Waarschuwing:<\/span>\n    Het invullen van een aanvraag kost ongeveer 10 minuten. U kunt uw aanvraag tussentijds opslaan. Zorg ervoor dat u\n    alle gevraagde documenten digitaal bij de hand heeft. Dit kan bijvoorbeeld een scan, schermafdruk of foto vanaf uw\n    mobiele telefoon zijn. Laat uw aanvraag niet langer dan noodzakelijk open staan op uw computer.\n<\/p>\n\n<h2>Introductie<\/h2>\n<p>\n    Voor artsen in opleiding tot Arts Internationale Gezondheid en Tropengeneeskunde brengt de eindstage van 6 maanden\n    in het buitenland hoge kosten met zich mee. De subsidie Opleidingsactiviteiten AIGT 2021-2026 compenseert deze\n    kosten.\n<\/p>\n<p>\n    De subsidie is een eenmalige vaste bijdrage voor de kosten die de artsen maken tijdens het opleidingsonderdeel\n    Buitenland. Het gaat hierbij bijvoorbeeld om reis- en verblijfkosten, visa, werkvergunning, inentingen,\n    verzekeringen en communicatiemiddelen.\n<\/p>\n<h2>Belangrijke voorwaarden<\/h2>\n<p><\/p>\n<ol>\n    <li><b>Alleen onderdeel Buitenland van AIGT komt in aanmerking<\/b>\n        <p>\n            Alleen het opleidingsonderdeel Buitenland van de opleiding tot Arts Internationale Gezondheid en\n            Tropengeneeskunde komt in aanmerking voor deze subsidie. Andere (medische vervolg)opleidingen met soortgelijke\n            opleidingsactiviteiten komen niet in aanmerking.\n        <\/p>\n    <\/li>\n    <li>\n        <b>Bewijs van opleidingsregister en van opleidingsinstituut nodig<\/b>\n        <p>Bij de aanvraag levert u de volgende documenten in:<\/p>\n        <ol>\n            <li>\n                bewijs van inschrijving in het opleidingsregister\n            <\/li>\n            <li>\n                bewijs dat u de opleiding volgt of heeft gevolgd\n            <\/li>\n        <\/ol>\n    <\/li>\n<\/ol>\n<p>Laat uw aanvraag niet langer dan noodzakelijk open staan op uw computer.<\/p>\n<h2>Aanvraag starten<\/h2>\n"}}]}],"options":{"required":[],"allOf":[]}},{"type":"CustomPageControl","label":"Persoonsgegevens toevoegen","elements":[{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"Group","elements":[{"type":"FormNotification","options":{"displayAs":"explanation","message":"U moet ouder zijn dan 18 jaar (artikel 4 van de regeling)."}}]},{"type":"Group","label":"Persoonlijke informatie","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstName","label":"Voornaam","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/infix","label":"Tussenvoegsel","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/lastName","label":"Achternaam","options":{"placeholder":""}}]}]},{"type":"Group","label":"Adres","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/street","label":"Straatnaam","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/houseNumber","label":"Huisnummer","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/houseNumberSuffix","label":"Huisnummer toevoeging","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/postalCode","label":"Postcode","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/city","label":"Plaatsnaam","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/country","label":"Land","options":{"format":"select","placeholder":"Selecteer een land"}}]}]},{"type":"Group","label":"Contact","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/phoneNumber","label":"Telefoonnummer","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/email","label":"E-mailadres","options":{"placeholder":"","tip":"U wordt via dit e-mailadres ge\u00efnformeerd over de status van uw aanvraag. Geef daarom alleen uw eigen e-mailadres door.","validation":["onBlur"]}}]}]},{"type":"Group","label":"Bank","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/bankAccountHolder","label":"Naam rekeninghouder","options":{"placeholder":"","validation":["onBlur"]}},{"type":"CustomControl","scope":"#\/properties\/bankAccountNumber","label":"IBAN","options":{"placeholder":"","tip":"Staat u onder bewind? Vermeld dan het IBAN van uw beheerrekening.","validation":["onValid"]}}]}]},{"type":"Group","label":"Opleiding","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/abroadCourseComponentStartDate","label":"Start opleidingsonderdeel buitenland","options":{"placeholder":""}}]}]}]}],"options":{"required":["firstName","lastName","street","houseNumber","postalCode","city","country","phoneNumber","email","bankAccountHolder","bankAccountNumber","abroadCourseComponentStartDate"],"allOf":[]}},{"type":"CustomPageControl","label":"Documenten toevoegen","elements":[{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"Group","label":"Documenten","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/proofOfRegistrationDocument","label":"Bewijs van inschrijving","options":{"accept":"image\/jpeg,image\/png,.pdf","maxFileSize":5242880,"minItems":1,"maxItems":20,"tip":"Upload de brief met het besluit van de Registratiecommissie Geneeskundig Specialisten (RGS) waarin wordt bevestigd dat u bent ingeschreven in het opleidingsregister voor het specialisme profielregister Internationale Gezondheidszorg en Tropengeneeskunde.\nDe bijlage bij de brief moet u niet indienen. Het overzicht met registratiegegevens verwerken wij namelijk niet bij de behandeling van de aanvraag. Toegestane bestandstypen: pdf, jpg, jpeg, png, Maximale bestandsgrootte: 20 MB."}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/educationalInstituteDeclarationDocument","label":"Start opleidingsonderdeel buitenland","options":{"accept":"image\/jpeg,image\/png,.pdf","maxFileSize":5242880,"minItems":1,"maxItems":20,"tip":"Upload een ondertekende verklaring van het Opleidingsinstituut Internationale Gezondheidszorg en Tropengeneeskunde (OIGT) waarin wordt bevestigd dat u in opleiding bent tot Arts IGT-KNMG en dat u het opleidingsonderdeel \u2018Buitenland\u2019 volgt of heeft gevolgd. Toegestane bestandstypen: pdf, jpg, jpeg, png, Maximale bestandsgrootte: 20 MB."}}]}]}]}],"options":{"required":["proofOfRegistrationDocument","educationalInstituteDeclarationDocument"],"allOf":[]}},{"type":"CustomPageControl","label":"Controleren en ondertekenen","elements":[{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"Group","label":"Controleren","elements":[{"type":"FormResultsTable","label":"Uw gegevens","options":{"fields":{"Naam":"{firstName} {infix} {lastName}","Adres":"{street} {houseNumber}{houseNumberSuffix} {postalCode} {city}","Telefoon":"{phoneNumber}","E-mailadres":"{email}"}}}]},{"type":"Group","label":"Ondertekenen","elements":[{"type":"CustomControl","scope":"#\/properties\/truthfullyCompleted","label":"Inhoud","options":{"description":"Ik verklaar het formulier naar waarheid te hebben ingevuld."}}]}]}],"options":{"required":["truthfullyCompleted"],"allOf":[]}}]}',
        '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"FormGroupControl","label":"Persoonlijke informatie","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Voornaam":"{firstName}","Tussenvoegsel":"{infix}","Achternaam":"{lastName}"}}}]},{"type":"FormGroupControl","label":"Adres","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Land":"{country}","Straatnaam":"{street}","Huisnummer":"{houseNumber}","Huisnummer toevoeging":"{houseNumberSuffix}","Postcode":"{postalCode}","Plaatsnaam":"{city}"}}}]},{"type":"FormGroupControl","label":"Contact","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Telefoonnummer":"{phoneNumber}","E-mailadres":"{email}"}}}]},{"type":"FormGroupControl","label":"Bank","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"IBAN":"{bankAccountNumber}","Naam rekeninghouder":"{bankAccountHolder}"}}}]},{"type":"FormGroupControl","label":"Opleiding","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Start opleidingsonderdeel buitenland":"{abroadCourseComponentStartDate}"}}}]},{"type":"FormGroupControl","label":"Bestanden","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Bewijs van inschrijving":"{proofOfRegistrationDocument}","Verklaring van opleidingsinstituut":"{educationalInstituteDeclarationDocument}"}}}]}]}');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('e5531a2e-4ac3-444a-b02d-92ac620927ed', '7075fcad-7d92-42f6-b46c-7733869019e0', 'firstAssessmentChecklist',
        'Gecontroleerd', null, 'multiselect',
        '{"options":["Valt de aanvrager onder de WSNP\/bewindvoering?","Is de aanvraag tijdig ingediend?","Is het aanvraagformulier volledig ingevuld?","Is het aanvraagformulier juist ondertekend?","Bevat de aanvraag alle vereiste documenten?","Hebben alle ingediende documenten betrekking op de juiste persoon?","Zijn het inschrijvingsbewijs RGS en het opleidingsbewijs OIGT correct ondertekend?","Staat de zakenpartner correct in SAP met het juiste bankrekeningnummer?","Is de einddatum van de buitenlandstage duidelijk?","Komt dit overeen met de opgave van de OIGT?","Komt de aanvrager voor in het M&O-register?"]}',
        false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('85cf5008-c6fd-46ee-9b4f-13fa56c27478', '7075fcad-7d92-42f6-b46c-7733869019e0', 'subsidyAwardedBefore',
        'Reeds eerder subsidie verleend aan dezelfde persoon voor de buitenlandstage?', null, 'select',
        '{"options":["Niet eerder subsidie verstrekt","Wel eerder subsidie verstrekt"],"default":null}', false, null,
        'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('8aacf268-69fd-4c40-b10b-637c1dce1677', '7075fcad-7d92-42f6-b46c-7733869019e0', 'amount', 'Bedrag', null,
        'select', '{"options":["\u20ac 17.000"],"default":"\u20ac 17.000"}', false, '{
        "type": "comparison",
        "stage": 2,
        "value": "Goedgekeurd",
        "operator": "===",
        "fieldCode": "firstAssessment"
    }', 'short', true);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('b84a0bd7-6a23-4c00-af49-4aca220ea5c9', '7075fcad-7d92-42f6-b46c-7733869019e0', 'firstAssessment',
        'Beoordeling', null, 'select', '{"options":["Aanvulling nodig","Afgekeurd","Goedgekeurd"],"default":null}', true,
        null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('93da2e86-8c9e-41ef-8596-952e29bfb553', '7075fcad-7d92-42f6-b46c-7733869019e0',
        'firstAssessmentRequestedComplementReason', 'Reden', null, 'select',
        '{"options":["Incomplete aanvraag","Onduidelijkheid of vervolgvragen"],"default":null}', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('824e3a2f-698f-4677-82e9-b94d36e0d9bc', '7075fcad-7d92-42f6-b46c-7733869019e0',
        'firstAssessmentRequestedComplementNote', 'Toelichting van benodigde aanvullingen', null, 'text',
        '{"maxLength":null}', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('6e656c91-b276-4842-aed7-14d1c862606a', '7075fcad-7d92-42f6-b46c-7733869019e0', 'firstAssessmentRejectedNote',
        'Reden van afkeuring', null, 'text', '{"maxLength":null}', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('d1df4a21-b10b-4323-9c77-b5c263b2dc75', '7075fcad-7d92-42f6-b46c-7733869019e0', 'firstAssessmentInternalNote',
        'Interne notitie', null, 'text', '{"maxLength":null}', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('4e2c5b8a-6eba-4414-acb5-f9b269056896', '0838f8a9-b2ff-4669-9d42-1c51a1134a34', 'firstAssessorMotivatedValid',
        'De motivatie van de eerste behandelaar is duidelijk en correct', null, 'checkbox', 'null', false, null, 'short',
        false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('1e5e519f-bbe7-421f-ae07-0faacfcbc84b', '0838f8a9-b2ff-4669-9d42-1c51a1134a34', 'internalAssessment',
        'Beoordeling', null, 'select',
        '{"options":["Oneens met de eerste beoordeling","Eens met de eerste beoordeling"],"default":null}', true, null,
        'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('1f924b16-65c7-4e09-9b04-8846abd86dda', '0838f8a9-b2ff-4669-9d42-1c51a1134a34',
        'internalAssessmentInternalNote', 'Interne notitie', null, 'text', '{"maxLength":null}', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('f3a7bc3e-2571-4af0-9744-3c1c782567e7', 'e5da8f2e-db87-45df-8967-ea3dceb2b207',
        'implementationCoordinatorAssessment', 'Beoordeling', null, 'select',
        '{"options":["Oneens met de eerste beoordeling","Eens met de eerste beoordeling"],"default":null}', true, null,
        'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('9549a56b-a79f-49f4-8bd4-b08bb48a76f3', 'e5da8f2e-db87-45df-8967-ea3dceb2b207',
        'implementationCoordinatorAssessmentInternalNote', 'Interne notitie', null, 'text', '{"maxLength":null}', false,
        null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('5fbfcf63-9482-4b3f-8fb7-9ed552e69183', 'e5da8f2e-db87-45df-8967-ea3dceb2b207',
        'implementationCoordinatorReasonForRejection', 'Reden van afkeuring', null, 'text', '{"maxLength":null}', false,
        null, 'short', false);

insert into public.subsidy_stage_uis ("id", "subsidy_stage_id", "version", "status", "input_ui", "view_ui")
values ('4aa24ca1-0fa8-45d3-a632-15fd788fbc6e', '7075fcad-7d92-42f6-b46c-7733869019e0', 1, 'published',
        '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Beoordeling","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentChecklist","options":{"format":"checkbox-group"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/subsidyAwardedBefore","options":{"format":"radio"}}]}]},{"type":"Group","label":"Uitkering","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/amount","options":{"format":"radio"}}]}]},{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessment","options":{"format":"radio"}}]}]},{"type":"Group","label":"Toelichting","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRequestedComplementReason","options":{"format":"radio"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRequestedComplementNote","options":{"format":"textarea","tip":"Deze notitie wordt opgenomen binnen de brief aan de aanvrager."},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRejectedNote","options":{"format":"textarea","tip":"Deze notitie wordt opgenomen binnen de brief aan de aanvrager."},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Afgekeurd"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentInternalNote","options":{"format":"textarea"}}]}]}]}',
        '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"FormGroupControl","label":"Eerste beoordeling","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentChecklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#\/properties\/subsidyAwardedBefore","options":{"readonly":true}}]},{"type":"FormGroupControl","label":"Uitkering","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/amount","options":{"readonly":true}}]},{"type":"FormGroupControl","label":"Eerste beoordeling","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRequestedComplementReason","options":{"readonly":true,"format":"radio"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}},{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRequestedComplementNote","options":{"readonly":true,"format":"textarea"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}},{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRejectedNote","options":{"readonly":true,"format":"textarea"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Afgekeurd"}}}},{"type":"CustomControl","scope":"#\/properties\/firstAssessmentInternalNote","options":{"readonly":true,"format":"textarea"}}]}]}');

insert into public.subsidy_stage_uis ("id", "subsidy_stage_id", "version", "status", "input_ui", "view_ui")
values ('8f7b2a5f-050e-4dd2-9d05-4e1d20f3929a', '0838f8a9-b2ff-4669-9d42-1c51a1134a34', 1, 'published',
        '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessorMotivatedValid","options":{"format":"checkbox"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/internalAssessment","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/internalAssessmentInternalNote","options":{"format":"textarea"}}]}]}]}',
        '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessorMotivatedValid","options":{"readonly":true}},{"type":"CustomControl","scope":"#\/properties\/internalAssessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/interalAssessmentInternalNote","options":{"readonly":true,"format":"textarea"}}]}');

insert into public.subsidy_stage_uis ("id", "subsidy_stage_id", "version", "status", "input_ui", "view_ui")
values ('6a669ec1-e949-40d8-bbc4-946665553fb1', 'e5da8f2e-db87-45df-8967-ea3dceb2b207', 1, 'published',
        '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/implementationCoordinatorAssessment","options":{"format":"radio"}}]}]},{"type":"Group","label":"Toelichting","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/implementationCoordinatorReasonForRejection","options":{"format":"textarea"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/implementationCoordinatorAssessment","schema":{"const":"Oneens met de eerste beoordeling"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/implementationCoordinatorAssessmentInternalNote","options":{"format":"textarea"}}]}]}]}',
        '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"CustomControl","scope":"#\/properties\/implementationCoordinatorAssessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/implementationCoordinatorAssessmentInternalNote","options":{"readonly":true,"format":"textarea"}},{"type":"CustomControl","scope":"#\/properties\/implementationCoordinatorReasonForRejection","options":{"readonly":true,"format":"textarea"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/implementationCoordinatorAssessment","schema":{"const":"Oneens met de eerste beoordeling"}}}}]}');

insert into public.subsidy_stage_transitions ("id", "description", "current_subsidy_stage_id", "target_subsidy_stage_id",
                                         "target_application_status", "condition", "send_message",
                                         "assign_to_previous_assessor", "clone_data")
values ('24a47df1-fc9d-4557-9012-d51738e5bdec', 'Aanvraag ingediend', 'a0f9ed92-c553-42d9-aef6-707bdfadd2d1',
        '7075fcad-7d92-42f6-b46c-7733869019e0', 'submitted', null, false, true, true);

insert into public.subsidy_stage_transitions ("id", "description", "current_subsidy_stage_id", "target_subsidy_stage_id",
                                         "target_application_status", "condition", "send_message", "clone_data")
values ('2f2e080d-0a05-467a-aaa5-292a95a6d361', 'Aanvulling gevraagd', '7075fcad-7d92-42f6-b46c-7733869019e0',
        'a0f9ed92-c553-42d9-aef6-707bdfadd2d1', 'requestForChanges',
        '{"type":"comparison","stage":2,"fieldCode":"firstAssessment","operator":"===","value":"Aanvulling nodig"}', true,
        true);

insert into public.subsidy_stage_transitions ("id", "description", "current_subsidy_stage_id", "target_subsidy_stage_id",
                                         "condition", "send_message")
values ('38957187-d17f-4e77-b4b2-90797f76b521', 'Eerste beoordeling voltooid', '7075fcad-7d92-42f6-b46c-7733869019e0',
        '0838f8a9-b2ff-4669-9d42-1c51a1134a34',
        '{"type":"in","stage":2,"fieldCode":"firstAssessment","values":["Goedgekeurd","Afgekeurd"]}', false);

insert into public.subsidy_stage_transitions ("id", "description", "current_subsidy_stage_id", "target_subsidy_stage_id",
                                         "condition", "send_message", "assign_to_previous_assessor", "clone_data")
values ('04811943-3e98-4532-940f-5b49908a193d', 'Interne beoordeling oneens met eerste beoordeling',
        '0838f8a9-b2ff-4669-9d42-1c51a1134a34', '7075fcad-7d92-42f6-b46c-7733869019e0',
        '{"type":"comparison","stage":3,"fieldCode":"internalAssessment","operator":"===","value":"Oneens met de eerste beoordeling"}',
        false, true, true);

insert into public.subsidy_stage_transitions ("id", "description", "current_subsidy_stage_id", "target_subsidy_stage_id",
                                         "target_application_status", "condition", "send_message")
values ('4d3e230b-dec5-4c62-b6d9-8aea62819234', 'Interne beoordeling eens met afkeuring eerste beoordeling',
        '0838f8a9-b2ff-4669-9d42-1c51a1134a34', null, 'rejected',
        '{"type":"and","conditions":[{"type":"comparison","stage":2,"fieldCode":"firstAssessment","operator":"===","value":"Afgekeurd"},{"type":"comparison","stage":3,"fieldCode":"internalAssessment","operator":"===","value":"Eens met de eerste beoordeling"}]}',
        true);

insert into public.subsidy_stage_transitions ("id", "description", "current_subsidy_stage_id", "target_subsidy_stage_id",
                                         "condition", "send_message")
values ('d5a683bb-23bc-4c14-8ae2-2b2e62d378bb', 'Interne beoordeling eens met goedkeuring eerste beoordeling',
        '0838f8a9-b2ff-4669-9d42-1c51a1134a34', 'e5da8f2e-db87-45df-8967-ea3dceb2b207',
        '{"type":"and","conditions":[{"type":"comparison","stage":2,"fieldCode":"firstAssessment","operator":"===","value":"Goedgekeurd"},{"type":"comparison","stage":3,"fieldCode":"internalAssessment","operator":"===","value":"Eens met de eerste beoordeling"}]}',
        false);

insert into public.subsidy_stage_transitions ("id", "description", "current_subsidy_stage_id", "target_subsidy_stage_id",
                                         "condition", "send_message", "assign_to_previous_assessor", "clone_data")
values ('e4eb01fb-2acf-469c-9ffe-9a0a8be04752', 'Interne beoording oneens met eerste beoordeling',
        'e5da8f2e-db87-45df-8967-ea3dceb2b207', '7075fcad-7d92-42f6-b46c-7733869019e0',
        '{"type":"comparison","stage":4,"fieldCode":"implementationCoordinatorAssessment","operator":"===","value":"Oneens met de eerste beoordeling"}',
        false, true, true);

insert into public.subsidy_stage_transitions ("id", "description", "current_subsidy_stage_id", "target_subsidy_stage_id",
                                         "target_application_status", "condition", "send_message")
values ('72bc33b6-2fbe-4d05-bd3b-0e9e88adb76a', 'Interne beoordeling eens met eerste beoordeling',
        'e5da8f2e-db87-45df-8967-ea3dceb2b207', null, 'approved',
        '{"type":"comparison","stage":4,"fieldCode":"implementationCoordinatorAssessment","operator":"===","value":"Eens met de eerste beoordeling"}',
        true);

insert into "subsidy_stage_transition_messages" ("id", "subsidy_stage_transition_id", "version", "status", "created_at",
                                                 "subject", "content_pdf", "content_html")
values ('c6410597-cbc0-45f4-aa0c-3d8631d661f2', '2f2e080d-0a05-467a-aaa5-292a95a6d361', 1, 'published',
        '2024-01-23 12:47:44', 'Aanvulling nodig', '{layout ''letter_layout.latte''}

{block concern}
    Betreft: Verzoek om aanvulling aanvraag ''Regeling {$content->subsidyTitle}''
{/block}

{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de ''Regeling {$content->subsidyTitle}'' met referentienummer: {$content->reference}.
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
', '{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de ''Regeling {$content->subsidyTitle}'' met referentienummer: {$content->reference}.
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
');

insert into "subsidy_stage_transition_messages" ("id", "subsidy_stage_transition_id", "version", "status", "created_at",
                                                 "subject", "content_pdf", "content_html")
values ('b135a0f1-c584-4f69-bbad-e9db91a0de6d', '4d3e230b-dec5-4c62-b6d9-8aea62819234', 1, 'published',
        '2024-01-23 12:47:44', 'Aanvraag afgekeurd', '{layout ''letter_layout.latte''}

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

    {if $content->stage2->firstAssessmentRejectedNote}
        <h2>Motivering bij het besluit</h2>
        <p>Uw aanvraag wordt afgewezen vanwege de volgende reden(en):</p>
        <p>{$content->stage2->firstAssessmentRejectedNote|breakLines}</p>
        <p>&nbsp;</p>
    {/if}
{/block}

{block sidebar}
    {include parent}
{/block}
', '{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>
    <p>&nbsp;</p>

    <h2>Besluit</h2>
    <p>Uw aanvraag moet ik helaas afwijzen. De reden(en) licht ik hieronder toe.</p>
    <p>&nbsp;</p>

    {if $content->stage2->firstAssessmentRejectedNote}
        <h2>Motivering bij het besluit</h2>
        <p>Uw aanvraag wordt afgewezen vanwege de volgende reden(en):</p>
        <p>{$content->stage2->firstAssessmentRejectedNote|breakLines}</p>
        <p>&nbsp;</p>
    {elseif $content->stage4->internalAssessmentReasonForRejection}
        <h2>Motivering bij het besluit</h2>
        <p>Uw aanvraag wordt afgewezen vanwege de volgende reden(en):</p>
        <p>{$content->stage4->internalAssessmentReasonForRejection|breakLines}</p>
        <p>&nbsp;</p>
    {/if}
{/block}
');

insert into "subsidy_stage_transition_messages" ("id", "subsidy_stage_transition_id", "version", "status", "created_at",
                                                 "subject", "content_pdf", "content_html")
values ('ef41a929-6556-4dec-975e-5d75f5a48a64', '72bc33b6-2fbe-4d05-bd3b-0e9e88adb76a', 1, 'published',
        '2024-01-23 12:47:44', 'Aanvraag goedgekeurd', '{layout ''letter_layout.latte''}

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
    <p>Hierbij verleen ik u de financiële tegemoetkoming/subsidie van {$content->stage2->amount}.</p>
    <p>&nbsp;</p>

     <h2>Wanneer ontvangt u de financiële ondersteuning?</h2>
      <p>Het bedrag van {$content->stage2->amount} wordt in één keer uitbetaald. Wij streven ernaar de financiële
          ondersteuning binnen 10 werkdagen uit te keren.</p>

{/block}

{block sidebar}
    {include parent}
{/block}
', '{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>
    <p>&nbsp;</p>

    <h2>Besluit</h2>
    <p>Hierbij verleen ik u de financiële tegemoetkoming/subsidie van {$content->stage2->amount}.</p>
    <p>&nbsp;</p>

    <h2>Wanneer ontvangt u de financiële ondersteuning?</h2>
        <p>Het bedrag van {$content->stage2->amount} wordt in één keer uitbetaald. Wij streven ernaar de financiële ondersteuning binnen 10 werkdagen uit te keren.</p>

{/block}
');

