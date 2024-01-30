insert into public.subsidies ("id", "title", "reference_prefix", "code", "description", "valid_from", "valid_to")
values ('00f26400-7232-475f-922c-6b569b7e421a', 'Borstprothesen transvrouwen', 'BTV24', 'BTV',
        'Transvrouwen zijn man-vrouw transgenders die negatieve gevoelens (''genderdysforie'') ervaren omdat ze als man geboren zijn en in transitie zijn om als vrouw te leven. De meerderheid van de transvrouwen vindt, ook na behandeling (de zogeheten genderbevestigende hormonale therapie), dat zij te weinig borstweefsel heeft voor een vrouwelijk profiel. Dit kan een grote hindernis zijn bij de transitie. Een borstvergroting kan deze hinder verminderen.',
        '2019-02-01', null);

insert into public.subsidy_versions ("id", "subsidy_id", "version", "status", "created_at", "subsidy_page_url",
                                "contact_mail_address", "mail_to_address_field_identifier",
                                "mail_to_name_field_identifier", "review_period")
values ('907bb399-0d19-4e1a-ac75-25a864df27c6', '00f26400-7232-475f-922c-6b569b7e421a', 1, 'published', '2019-02-01',
        'https://www.dus-i.nl/subsidies', 'dienstpostbus@minvws.nl', 'email', 'firstName;infix;lastName', 91);

insert into public.subsidy_stages ("id", "subsidy_version_id", "title", "subject_role", "stage")
values ('721c1c28-e674-415f-b1c3-872a631ed045', '907bb399-0d19-4e1a-ac75-25a864df27c6', 'Aanvraag', 'applicant', 1);

insert into public.subsidy_stages ("id", "subsidy_version_id", "title", "subject_role", "assessor_user_role", "stage",
                              "internal_note_field_code")
values ('6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c', '907bb399-0d19-4e1a-ac75-25a864df27c6', 'Eerste beoordeling',
        'assessor', 'assessor', 2, 'firstAssessmentInternalNote');

insert into public.subsidy_stages ("id", "subsidy_version_id", "title", "subject_role", "assessor_user_role", "stage",
                              "internal_note_field_code")
values ('b2b08566-8493-4560-8afa-d56402931f74', '907bb399-0d19-4e1a-ac75-25a864df27c6', 'Tweede beoordeling',
        'assessor', 'assessor', 3, 'secondAssessmentInternalNote');

insert into public.subsidy_stages ("id", "subsidy_version_id", "title", "subject_role", "assessor_user_role", "stage",
                              "internal_note_field_code")
values ('e456e790-1919-4a2b-b3d5-337d0053abe3', '907bb399-0d19-4e1a-ac75-25a864df27c6', 'Interne beoordeling',
        'assessor', 'internalAuditor', 4, 'internalAssessmentInternalNote');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval")
values ('7727fbbe-30e3-416a-9ee8-40c296d44bc8', '721c1c28-e674-415f-b1c3-872a631ed045', 'firstName', 'Voornaam', null,
        'text', '{"maxLength":null}', true, null, 'short');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval")
values ('ea5bd1cc-5212-42ce-b5cb-8df3ce4c1dad', '721c1c28-e674-415f-b1c3-872a631ed045', 'infix', 'Tussenvoegsel', null,
        'text', '{"maxLength":null}', false, null, 'short');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval")
values ('71353f55-3567-4332-8a16-73c659477453', '721c1c28-e674-415f-b1c3-872a631ed045', 'lastName', 'Achternaam', null,
        'text', '{"maxLength":null}', true, null, 'short');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval")
values ('e66a2481-1372-440a-a102-1ba2091eb2f0', '721c1c28-e674-415f-b1c3-872a631ed045', 'dateOfBirth', 'Geboortedatum',
        null, 'date', 'null', true, null, 'short');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval")
values ('e53764d2-55a9-439a-8332-be02115c9c27', '721c1c28-e674-415f-b1c3-872a631ed045', 'street', 'Straat', null,
        'text', '{"maxLength":null}', true, null, 'short');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval")
values ('7cdd7ab0-377b-4c3a-8f47-60dc6a8149f1', '721c1c28-e674-415f-b1c3-872a631ed045', 'houseNumber', 'Huisnummer',
        null, 'text:numeric', '{"maxLength":null}', true, null, 'short');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval")
values ('2afaa925-9c58-4868-88a4-65e8533592de', '721c1c28-e674-415f-b1c3-872a631ed045', 'houseNumberSuffix',
        'Huisnummer toevoeging', null, 'text', '{"maxLength":10}', false, null, 'short');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval")
values ('a6734843-1245-4dcd-a739-d82b19a0237a', '721c1c28-e674-415f-b1c3-872a631ed045', 'postalCode', 'Postcode', null,
        'custom:postalcode', 'null', false, null, 'short');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval")
values ('946a2717-1657-44c5-bd89-63ab603ebdac', '721c1c28-e674-415f-b1c3-872a631ed045', 'city', 'Plaats', null, 'text',
        '{"maxLength":100}', true, null, 'short');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval")
values ('fb9b9b0b-4a27-49e5-a56e-b43efc7afdef', '721c1c28-e674-415f-b1c3-872a631ed045', 'country', 'Land', null,
        'select',
        '{"options":["Afghanistan","\u00c5land","Albani\u00eb","Algerije","Amerikaanse Maagdeneilanden","Amerikaans-Samoa","Andorra","Angola","Anguilla","Antarctica","Antigua en Barbuda","Argentini\u00eb","Armeni\u00eb","Aruba","Australi\u00eb","Azerbeidzjan","Bahama\u2019s","Bahrein","Bangladesh","Barbados","Belgi\u00eb","Belize","Benin","Bermuda","Bhutan","Bolivia","Bosni\u00eb en Herzegovina","Botswana","Bouveteiland","Brazili\u00eb","Britse Maagdeneilanden","Brits Indische Oceaanterritorium","Brunei","Bulgarije","Burkina Faso","Burundi","Cambodja","Canada","Centraal-Afrikaanse Republiek","Chili","China","Christmaseiland","Cocoseilanden","Colombia","Comoren","Congo-Brazzaville","Congo-Kinshasa","Cookeilanden","Costa Rica","Cuba","Cura\u00e7ao","Cyprus","Denemarken","Djibouti","Dominica","Dominicaanse Republiek","Duitsland","Ecuador","Egypte","El Salvador","Equatoriaal-Guinea","Eritrea","Estland","Ethiopi\u00eb","Faer\u00f6er","Falklandeilanden","Fiji","Filipijnen","Finland","Frankrijk","Franse Zuidelijke en Antarctische Gebieden","Frans-Guyana","Frans-Polynesi\u00eb","Gabon","Gambia","Georgi\u00eb","Ghana","Gibraltar","Grenada","Griekenland","Groenland","Guadeloupe","Guam","Guatemala","Guernsey","Guinee","Guinee-Bissau","Guyana","Ha\u00efti","Heard en McDonaldeilanden","Honduras","Hongarije","Hongkong","Ierland","IJsland","India","Indonesi\u00eb","Irak","Iran","Isra\u00ebl","Itali\u00eb","Ivoorkust","Jamaica","Japan","Jemen","Jersey","Jordani\u00eb","Kaaimaneilanden","Kaapverdi\u00eb","Kameroen","Kazachstan","Kenia","Kirgizi\u00eb","Kiribati","Kleine Pacifische eilanden van de Verenigde Staten","Koeweit","Kroati\u00eb","Laos","Lesotho","Letland","Libanon","Liberia","Libi\u00eb","Liechtenstein","Litouwen","Luxemburg","Macau","Madagaskar","Malawi","Maldiven","Maleisi\u00eb","Mali","Malta","Marokko","Marshalleilanden","Martinique","Mauritani\u00eb","Mauritius","Mayotte","Mexico","Micronesia","Moldavi\u00eb","Monaco","Mongoli\u00eb","Montenegro","Montserrat","Mozambique","Myanmar","Namibi\u00eb","Nauru","Nederland","Nepal","Nicaragua","Nieuw-Caledoni\u00eb","Nieuw-Zeeland","Niger","Nigeria","Niue","Noordelijke Marianen","Noord-Korea","Noord-Macedoni\u00eb","code Land","Noorwegen","Norfolk","Oeganda","Oekra\u00efne","Oezbekistan","Oman","Oostenrijk","Oost-Timor","Pakistan","Palau","Palestina","Panama","Papoea-Nieuw-Guinea","Paraguay","Peru","Pitcairneilanden","Polen","Portugal","Puerto Rico","Qatar","R\u00e9union","Roemeni\u00eb","Rusland","Rwanda","Saint-Barth\u00e9lemy","Saint Kitts en Nevis","Saint Lucia","Saint-Pierre en Miquelon","Saint Vincent en de Grenadines","Salomonseilanden","Samoa","San Marino","Saoedi-Arabi\u00eb","Sao Tom\u00e9 en Principe","Senegal","Servi\u00eb","Seychellen","Sierra Leone","Singapore","Sint-Helena, Ascension en Tristan da Cunha","Sint-Maarten","Sint Maarten","Sloveni\u00eb","Slowakije","Soedan","Somali\u00eb","Spanje","Spitsbergen en Jan Mayen","Sri Lanka","Suriname","Swaziland","Syri\u00eb","Tadzjikistan","Taiwan","Tanzania","Thailand","Togo","Tokelau","Tonga","Trinidad en Tobago","Tsjaad","Tsjechi\u00eb","Tunesi\u00eb","Turkije","Turkmenistan","Turks- en Caicoseilanden","Tuvalu","Uruguay","Vanuatu","Vaticaanstad","Venezuela","Verenigde Arabische Emiraten","Verenigde Staten","Verenigd Koninkrijk","Vietnam","Wallis en Futuna","Westelijke Sahara","Wit-Rusland","Zambia","Zimbabwe","Zuid-Afrika","Zuid-Georgia en de Zuidelijke Sandwicheilanden","Zuid-Korea","Zuid-Soedan","Zweden","Zwitserland"],"default":"Nederland"}',
        true, null, 'short');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval")
values ('510d6c27-8993-4df7-8cc5-50863da5ca4e', '721c1c28-e674-415f-b1c3-872a631ed045', 'phoneNumber', 'Telefoonnummer',
        null, 'text:tel', '{"maxLength":20}', false, null, 'short');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval")
values ('8c9a7ac3-70a0-4292-8e99-9664baffaa45', '721c1c28-e674-415f-b1c3-872a631ed045', 'email', 'E-mailadres', null,
        'text:email', '{"maxLength":300}', false, null, 'short');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval")
values ('0267aaf7-b364-4e02-b768-1e35eb5ac1e1', '721c1c28-e674-415f-b1c3-872a631ed045', 'bankAccountNumber', 'IBAN',
        null, 'custom:bankaccount', 'null', true, null, 'short');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval")
values ('01ae81e1-2eb9-4693-85cc-e42189bc27af', '721c1c28-e674-415f-b1c3-872a631ed045', 'bankAccountHolder',
        'Naam rekeninghouder', null, 'text', '{"maxLength":50}', true, null, 'short');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval")
values ('40e92488-9f11-490d-8de1-d645f762a534', '721c1c28-e674-415f-b1c3-872a631ed045', 'bankStatement',
        'Kopie bankafschrift', null, 'upload',
        '{"mimeTypes":["image\/jpeg","image\/png","application\/pdf"],"maxFileSize":5242880,"minItems":1,"maxItems":20}',
        true, null, 'short');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval")
values ('1c171899-4310-42ca-b1ba-b665f96bf6ba', '721c1c28-e674-415f-b1c3-872a631ed045',
        'extractPopulationRegisterDocument', 'Uittreksel bevolkingsregister niet ouder dan 3 maanden', null, 'upload',
        '{"mimeTypes":["image\/jpeg","image\/png","application\/pdf"],"maxFileSize":5242880,"minItems":1,"maxItems":20}',
        true, null, 'short');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval")
values ('7f989a5a-9fdd-46c9-872d-d0afb58aa5d2', '721c1c28-e674-415f-b1c3-872a631ed045',
        'proofOfMedicalTreatmentDocument', 'Verklaring behandeltraject', null, 'upload',
        '{"mimeTypes":["image\/jpeg","image\/png","application\/pdf"],"maxFileSize":5242880,"minItems":1,"maxItems":20}',
        true, null, 'short');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval")
values ('c5de234c-4aa4-4185-9a23-9a2c9ee54a47', '721c1c28-e674-415f-b1c3-872a631ed045',
        'proofOfTypeOfMedicalTreatmentDocument', 'Verklaring type behandeling', null, 'upload',
        '{"mimeTypes":["image\/jpeg","image\/png","application\/pdf"],"maxFileSize":5242880,"minItems":1,"maxItems":20}',
        true, null, 'short');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval")
values ('922357f9-01e0-436a-8b24-13dad16a4374', '721c1c28-e674-415f-b1c3-872a631ed045',
        'permissionToProcessPersonalData',
        'Ik geef toestemming voor het verwerken van mijn persoonsgegevens voor deze subsidieaanvraag. Ik verklaar het formulier naar waarheid te hebben ingevuld.',
        null, 'checkbox', 'null', true, null, 'short');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval")
values ('72b2291a-b12a-459e-bee0-6fa9fbe62d46', '721c1c28-e674-415f-b1c3-872a631ed045', 'truthfullyCompleted', '', null,
        'checkbox', 'null', true, null, 'short');

insert into public.subsidy_stage_uis ("id", "subsidy_stage_id", "version", "status", "input_ui", "view_ui")
values ('72475863-7987-4375-94d7-21e04ff6552b', '721c1c28-e674-415f-b1c3-872a631ed045', 1, 'published',
        '{"type":"CustomPageNavigationControl","elements":[{"type":"CustomPageControl","label":"start","elements":[{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"FormHtml","options":{"html":"<p class=\"warning\">\n    <span>Waarschuwing:<\/span>\n    Het invullen van een aanvraag kost ongeveer 10 minuten. Als u meer tijd nodig heeft, dan heeft dit\n    geen consequenties (gegevens blijven behouden). Zorg ervoor dat u alle gevraagde documenten\n    digitaal bij de hand heeft. Dit kan bijvoorbeeld een scan, schermafdruk of foto vanaf uw mobiele\n    telefoon zijn. Laat uw aanvraag niet langer dan noodzakelijk open staan op uw computer.\n<\/p>\n\n<h2>Introductie<\/h2>\n<p>\n    Bent u een transvrouw met genderdysforie? En bevindt u zich in een medisch transitietraject en\n    overweegt u een borstvergroting? U kunt dan in aanmerking komen voor een subsidie via de\n    subsidieregeling Borstprothesen transvrouwen. Met dit formulier vraagt u een subsidie aan voor een\n    plastisch-chirurgische borstconstructie. Deze behandeling moet bewezen effectief zijn.\n<\/p>\n<h2>Benodigde bestanden<\/h2>\n<p>Om in aanmerking te komen voor de subsidie, worden een aantal documenten aan u gevraagd:<\/p>\n<p><\/p>\n<ol>\n    <li>Uittreksel Basisregistratie Personen;<\/li>\n    <li>Kopie van een recent bankafschrift of bankpas;<\/li>\n    <li>\n        Medische verklaring van uw BIG-geregistreerde arts over de behandeling die u tot nu toe heeft\n        gevolgd. Download het\n        <a\n            title=\"Medische verklaring behandeltraject format\"\n            href=\"https:\/\/www.dus-i.nl\/subsidies\/borstprothesen-transvrouwen\/documenten\/publicaties\/2019\/01\/14\/verklaring-behandelend-arts-borstprothesen-transvrouwen\"\n            target=\"_blank\"\n            rel=\"nofollow noopener external\"\n        >Medische verklaring behandeltraject format<\/a\n        >.\n    <\/li>\n    <li>\n        Medische verklaring van een BIG-geregistreerde arts over de behandeling (operatie) die zal\n        worden uitgevoerd. Download het\n        <a\n            title=\"Medische verklaring van het type behandeling format\"\n            href=\"https:\/\/www.dus-i.nl\/subsidies\/borstprothesen-transvrouwen\/documenten\/publicaties\/2021\/08\/05\/medische-verklaring-van-het-type-behandeling-borstprothesen-transvrouwen\"\n            target=\"_blank\"\n            rel=\"nofollow noopener external\"\n        >Medische verklaring van het type behandeling format<\/a\n        >.\n    <\/li>\n<\/ol>\n<p>In de laatste stap van het aanvraagformulier kunt u deze uploaden.<\/p>\n\n<h2>Aanvraag invullen<\/h2>\n<p>\n    Het invullen van een aanvraag kost ongeveer 10 minuten. Als u meer tijd nodig heeft, dan heeft dit\n    geen consequenties. Uw reeds ingevulde gegevens blijven behouden.\n<\/p>\n<p>\n    Zorg ervoor dat u alle gevraagde documenten digitaal bij de hand heeft. Dit kan bijvoorbeeld een\n    scan, schermafdruk of foto vanaf uw mobiele telefoon zijn.\n<\/p>\n<p>Laat uw aanvraag niet langer dan noodzakelijk open staan op uw computer.<\/p>\n<h2>Aanvraag starten<\/h2>\n"}},{"type":"FormGroupControl","label":"Toestemming","options":{"section":true,"group":true},"elements":[{"type":"CustomControl","scope":"#\/properties\/permissionToProcessPersonalData","label":"Verwerking","options":{"description":"Ik geef toestemming voor het verwerken van mijn persoonsgegevens voor deze aanvraag."}}]}]}],"options":{"required":["permissionToProcessPersonalData"],"allOf":[]}},{"type":"CustomPageControl","label":"Persoonsgegevens toevoegen","elements":[{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"Group","elements":[{"type":"FormNotification","options":{"displayAs":"explanation","message":"U moet ouder zijn dan 18 jaar (artikel 4 van de regeling)."}}]},{"type":"Group","label":"Persoonlijke informatie","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstName","label":"Voornaam","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/infix","label":"Tussenvoegsel","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/lastName","label":"Achternaam","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/dateOfBirth","label":"Geboortedatum","options":{"placeholder":""}}]}]},{"type":"Group","label":"Adres","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/street","label":"Straatnaam","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/houseNumber","label":"Huisnummer","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/houseNumberSuffix","label":"Huisnummer toevoeging","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/postalCode","label":"Postcode","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/city","label":"Plaatsnaam","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/country","label":"Land","options":{"format":"select","placeholder":"Selecteer een land"}}]}]},{"type":"Group","label":"Contact","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/phoneNumber","label":"Telefoonnummer","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/email","label":"E-mailadres","options":{"placeholder":"","tip":"U wordt via dit e-mailadres ge\u00efnformeerd over de status van uw aanvraag. Geef daarom alleen uw eigen e-mailadres door.","validation":["onBlur"]}}]}]},{"type":"Group","label":"Bank","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/bankAccountHolder","label":"Naam rekeninghouder","options":{"placeholder":"","validation":["onBlur"]}},{"type":"CustomControl","scope":"#\/properties\/bankAccountNumber","label":"IBAN","options":{"placeholder":"","tip":"Staat u onder bewind? Vermeld dan het IBAN van uw beheerrekening.","validation":["onValid"]}}]}]}]}],"options":{"required":["firstName","lastName","street","dateOfBirth","houseNumber","postalCode","city","country","phoneNumber","email","bankAccountHolder","bankAccountNumber"],"allOf":[]}},{"type":"CustomPageControl","label":"Documenten toevoegen","elements":[{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"Group","label":"Bankafschrift of bankpas","elements":[{"type":"VerticalLayout","elements":[{"type":"FormResultsTable","label":"Uw bankgegegvens","options":{"fields":{"Naam rekeninghouder":"{bankAccountHolder}","IBAN":"{bankAccountNumber}"}}},{"type":"CustomControl","scope":"#\/properties\/bankStatement","label":"Kopie bankafschrift","options":{"accept":"image\/jpeg,image\/png,.pdf","maxFileSize":5242880,"minItems":1,"maxItems":20,"tip":"Op de kopie van een recent bankafschrift moeten het rekeningnummer en uw naam zichtbaar zijn. Adres en datum mogen ook, maar zijn niet verplicht. Maak de andere gegevens onleesbaar. U mag ook een afdruk van internet bankieren gebruiken of een kopie van uw bankpas. Zie ook dit <a title=\"voorbeeld\" href=\"#\" target=\"_blank\" rel=\"noopener\" class=\"external\">voorbeeld<\/a>. Toegestane bestandstypen: pdf, jpg, jpeg, png, Maximale bestandsgrootte: 5 MB."}}]}]},{"type":"Group","label":"Uittreksel bevolkingsregister","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/extractPopulationRegisterDocument","label":"Uittreksel bevolkingsregister","options":{"accept":"image\/jpeg,image\/png,.pdf","maxFileSize":5242880,"minItems":1,"maxItems":20,"tip":"U kunt een uittreksel uit het bevolkingsregister (de Basisregistratie personen) opvragen bij de gemeente waar u staat ingeschreven. Dit document bevat uw naam, geboortedatum en adres. Toegestane bestandstypen: pdf, jpg, jpeg, png, Maximale bestandsgrootte: 5 MB."}}]}]},{"type":"Group","label":"Medische verklaring behandeltraject","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/proofOfMedicalTreatmentDocument","label":"Verklaring behandeltraject","options":{"accept":"image\/jpeg,image\/png,.pdf","maxFileSize":5242880,"minItems":1,"maxItems":20,"tip":"De <a title=\"medische verklaring over het behandeltraject\" href=\"#\" target=\"_blank\" rel=\"noopener\" class=\"external\">medische verklaring over het behandeltraject<\/a> dat u tot nu toe heeft gevolgd moet zijn ingevuld door de BIG-geregistreerde arts waar u in behandeling bent. Dit kan een huisarts of medisch specialist zijn die de hormonen voorschrijft en de behandeling begeleidt. De verklaring mag niet ouder zijn dan twee maanden. Toegestane bestandstypen: pdf, jpg, jpeg, png, Maximale bestandsgrootte: 5 MB."}}]}]},{"type":"Group","label":"Medische verklaring van het type behandeling","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/proofOfTypeOfMedicalTreatmentDocument","label":"Verklaring type behandeling","options":{"accept":"image\/jpeg,image\/png,.pdf","maxFileSize":5242880,"minItems":1,"maxItems":20,"tip":"De <a title=\"medische verklaring van het type behandeling\" href=\"#\" target=\"_blank\" rel=\"noopener\" class=\"external\">medische verklaring van het type behandeling<\/a> (operatie) dat zal worden uitgevoerd moet zijn ingevuld en ondertekend door een BIG-geregistreerde arts. Toegestane bestandstypen: pdf, jpg, jpeg, png, Maximale bestandsgrootte: 5 MB."}}]}]}]}],"options":{"required":["bankStatement","extractPopulationRegisterDocument","proofOfMedicalTreatmentDocument","proofOfTypeOfMedicalTreatmentDocument"],"allOf":[]}},{"type":"CustomPageControl","label":"Controleren en ondertekenen","elements":[{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"Group","label":"Controleren","elements":[{"type":"FormResultsTable","label":"Uw bankgegevens","options":{"fields":{"Indiener":"{firstName} {infix} {lastName}","Adres":"{streetName} {houseNumber}{houseNumberSuffix} {postalCode} {city}","Telefoon":"{phoneNumber}","E-mailadres":"{email}"}}}]},{"type":"Group","label":"Ondertekenen","elements":[{"type":"CustomControl","scope":"#\/properties\/truthfullyCompleted","label":"Inhoud","options":{"description":"Ik verklaar het formulier naar waarheid te hebben ingevuld."}}]}]}],"options":{"required":["truthfullyCompleted"],"allOf":[]}}]}',
        '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"FormGroupControl","label":"Metagegevens","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Dossiernummer":"{assessmentId}","Aangevraagd op":"{validFrom}","Uiterste behandeldatum":"{validTo}"}}}]},{"type":"FormGroupControl","label":"Persoonlijke informatie","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Voornaam":"{firstName}","Tussenvoegsel":"{infix}","Achternaam":"{lastName}","Geboortedatum":"{dateOfBirth}"}}}]},{"type":"FormGroupControl","label":"Adres","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Land":"{country}","Straatnaam":"{street}","Huisnummer":"{houseNumber}","Huisnummer toevoeging":"{houseNumberSuffix}","Postcode":"{postalCode}","Plaatsnaam":"{city}"}}}]},{"type":"FormGroupControl","label":"Contact","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Telefoonnummer":"{phoneNumber}","E-mailadres":"{email}"}}}]},{"type":"FormGroupControl","label":"Bank","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"IBAN":"{bankAccountNumber}","Naam rekeninghouder":"{bankAccountHolder}"}}}]},{"type":"FormGroupControl","label":"Bestanden","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Kopie bankafschrift":"{bankStatement}","Uitreksel bevolkingsregister":"{extractPopulationRegisterDocument}","Verklaring behandeltraject":"{proofOfMedicalTreatmentDocument}","Verklaring type behandeling":"{proofOfTypeOfMedicalTreatmentDocument}"}}}]}]}');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval")
values ('3d5eb415-bbd6-4486-969b-db0bde65718f', '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c', 'firstAssessmentChecklist',
        'Gecontroleerd', null, 'multiselect',
        '{"options":["Uittreksel van het BRP is opgestuurd","De aanvrager is een ingezetene (> 4 maanden) in Nederland","de aanvrager is ouder dan 18 jaar","De verklaring van de arts over het behandeltraject is opgestuurd","De verklaring van de arts over het behandeltraject is minder dan 2 maanden oud","De verklaring van de arts over het behandeltraject is ondertekend en voorzien van een naamstempel","Het opgegeven BIG-nummer komt overeen met het BIG-register","De operatie heeft nog niet plaatsgevonden","De aanvrager heeft genderdysforie","De aanvrager heeft minimaal een jaar voor de aanvraag hormoonbehandeling ondergaan, of is hiermee vanwege medische redenen gestopt of kon deze om medische redenen niet ondergaan","De verklaring van de arts met de vermelding van de type behandeling is opgestuurd","De verklaring van de arts met de vermelding van de type behandeling is ondertekend en voorzien van een naamstempel","De type behandeling voldoet aan de voorwaarden conform de subsidieregeling","Het IBAN-nummer klopt met het opgegeven IBAN-nummer van de aanvraag","De tenaamstelling op het bankafschrift of bankpas klopt"]}',
        true, null, 'short');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval")
values ('33f4c629-467f-4d9b-8e80-dfaeafde8d36', '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c', 'amount', 'Bedrag', null,
        'select', '{"options":["\u20ac 3.830","\u20ac 13.720"],"default":null}', true, null, 'short');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval")
values ('9f0f8c1b-c622-40c0-9fd9-f4c82a3e7025', '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c', 'firstAssessment',
        'Beoordeling', null, 'select', '{"options":["Aanvulling nodig","Afgekeurd","Goedgekeurd"],"default":null}', true,
        null, 'short');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval")
values ('af24ae79-6fe2-4ec3-b151-30c2dc06fbf1', '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c',
        'firstAssessmentRequestedComplementReason', 'Reden', null, 'select',
        '{"options":["Incomplete aanvraag","Onduidelijkheid of vervolgvragen"],"default":null}', false, null, 'short');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval")
values ('fbe20db8-ec97-4923-b6cc-38381e07d264', '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c',
        'firstAssessmentRequestedComplementNote', 'Toelichting van benodigde aanvullingen', null, 'text',
        '{"maxLength":null}', false, null, 'short');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval")
values ('158e37b5-40c5-4ea6-bcda-3eecc0663bd7', '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c', 'firstAssessmentRejectedNote',
        'Reden van afkeuring', null, 'text', '{"maxLength":null}', false, null, 'short');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval")
values ('d9efc1f7-daba-48d1-bc20-c93ca8ad506c', '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c', 'firstAssessmentInternalNote',
        'Interne notitie', null, 'text', '{"maxLength":null}', false, null, 'short');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval")
values ('0b4520c0-4dfa-439e-a56c-5a782b3d1cb8', 'b2b08566-8493-4560-8afa-d56402931f74', 'firstAssessorMotivatedValid',
        'De motivatie van de eerste behandelaar is duidelijk en correct', null, 'checkbox', 'null', false, null, 'short');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval")
values ('37d65c7c-5a7a-4c25-bebd-6f32046d111e', 'b2b08566-8493-4560-8afa-d56402931f74', 'secondAssessment',
        'Beoordeling', null, 'select',
        '{"options":["Oneens met de eerste beoordeling","Eens met de eerste beoordeling"],"default":null}', true, null,
        'short');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval")
values ('6b312a49-f943-40a6-aa0f-f62a4c6d7552', 'b2b08566-8493-4560-8afa-d56402931f74', 'secondAssessmentInternalNote',
        'Interne notitie', null, 'text', '{"maxLength":null}', false, null, 'short');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval")
values ('1ab9823d-737d-4138-93ce-b9f0130f4455', 'e456e790-1919-4a2b-b3d5-337d0053abe3', 'internalAssessmentChecklist',
        'Controlevragen', null, 'multiselect',
        '{"options":["Valt de aanvrager onder de WSNP\/bewindvoering?","Is er een relatienummer bekend in SAP?","Komen de NAW-gegevens overeen tussen SAP en Mijn DUS-I?","Komt het IBAN\/rekeningnummer overeen tussen SAP en Mijn DUS-I?","Zijn alle benodigde documenten aangeleverd?","Klopt het subsidiebedrag met de gekozen behandeling in de aangeleverde medische verklaring?","Is het subsidiebedrag juist vermeld in Mijn DUS-I en in de verplichting in SAP?"]}',
        false, null, 'short');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval")
values ('09b070e4-1d60-4794-a484-3ccea0b41d66', 'e456e790-1919-4a2b-b3d5-337d0053abe3', 'internalAssessment',
        'Beoordeling', null, 'select', '{"options":["Afgekeurd","Goedgekeurd"],"default":null}', true, null, 'short');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval")
values ('20f072b3-e881-47b4-ba2b-a29ad1881449', 'e456e790-1919-4a2b-b3d5-337d0053abe3',
        'internalAssessmentInternalNote', 'Interne notitie', null, 'text', '{"maxLength":null}', false, null, 'short');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval")
values ('44ab3128-bb25-4793-9b9b-f1db10520291', 'e456e790-1919-4a2b-b3d5-337d0053abe3',
        'internalAssessmentReasonForRejection', 'Reden van afkeuring', null, 'text', '{"maxLength":null}', false, null,
        'short');

insert into public.subsidy_stage_uis ("id", "subsidy_stage_id", "version", "status", "input_ui", "view_ui")
values ('db1076a1-42f3-4c90-b1bf-57d1db025f2e', '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c', 1, 'published',
        '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Beoordeling","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentChecklist","options":{"format":"checkbox-group"}}]}]},{"type":"Group","label":"Uitkering","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/amount","options":{"format":"radio"}}]}]},{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessment","options":{"format":"radio"}}]}]},{"type":"Group","label":"Toelichting","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRequestedComplementReason","options":{"format":"radio"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRequestedComplementNote","options":{"format":"textarea","tip":"Deze notitie wordt opgenomen binnen de brief aan de aanvrager."},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRejectedNote","options":{"format":"textarea","tip":"Deze notitie wordt opgenomen binnen de brief aan de aanvrager."},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Afgekeurd"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentInternalNote","options":{"format":"textarea"}}]}]}]}',
        '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"FormGroupControl","label":"Persoonsgegevens","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentChecklist","options":{"readonly":true,"format":"checkbox-group"}}]},{"type":"FormGroupControl","label":"Uitkering","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/amount","options":{"readonly":true}}]},{"type":"FormGroupControl","label":"Eerste beoordeling","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRequestedComplementReason","options":{"readonly":true,"format":"radio"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}},{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRequestedComplementNote","options":{"readonly":true,"format":"textarea"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}},{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRejectedNote","options":{"readonly":true,"format":"textarea"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Afgekeurd"}}}},{"type":"CustomControl","scope":"#\/properties\/firstAssessmentInternalNote","options":{"readonly":true,"format":"textarea"}}]}]}');

insert into public.subsidy_stage_uis ("id", "subsidy_stage_id", "version", "status", "input_ui", "view_ui")
values ('787c8ef4-bfcd-4bd4-aec1-dec02139c897', 'b2b08566-8493-4560-8afa-d56402931f74', 1, 'published',
        '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessorMotivatedValid","options":{"format":"checkbox"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/secondAssessment","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/secondAssessmentInternalNote","options":{"format":"textarea"}}]}]}]}',
        '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessorMotivatedValid","options":{"readonly":true}},{"type":"CustomControl","scope":"#\/properties\/secondAssessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/secondAssessmentInternalNote","options":{"readonly":true,"format":"textarea"}}]}');

insert into public.subsidy_stage_uis ("id", "subsidy_stage_id", "version", "status", "input_ui", "view_ui")
values ('a6080627-0ea9-436e-bbba-c454bd3809fd', 'e456e790-1919-4a2b-b3d5-337d0053abe3', 1, 'published',
        '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Checklist","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/internalAssessmentChecklist","options":{"format":"checkbox-group"}}]}]},{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/internalAssessment","options":{"format":"radio"}}]}]},{"type":"Group","label":"Toelichting","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/internalAssessmentReasonForRejection","options":{"format":"textarea","tip":"Deze notitie wordt opgenomen binnen de brief aan de aanvrager."},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/internalAssessment","schema":{"const":"Afgekeurd"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/internalAssessmentInternalNote","options":{"format":"textarea"}}]}]}]}',
        '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"CustomControl","scope":"#\/properties\/internalAssessmentChecklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#\/properties\/internalAssessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/internalAssessmentInternalNote","options":{"readonly":true,"format":"textarea"}},{"type":"CustomControl","scope":"#\/properties\/internalAssessmentReasonForRejection","options":{"readonly":true,"format":"textarea"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/internalAssessment","schema":{"const":"Afgekeurd"}}}}]}');

insert into public.subsidy_stage_transitions ("id", "description", "current_subsidy_stage_id", "target_subsidy_stage_id",
                                         "target_application_status", "condition", "send_message",
                                         "assign_to_previous_assessor", "clone_data")
values ('7a766078-8b8e-45c8-b04c-4a8de1fae275', 'Aanvraag ingediend', '721c1c28-e674-415f-b1c3-872a631ed045',
        '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c', 'submitted', null, false, true, true);

insert into public.subsidy_stage_transitions ("id", "description", "current_subsidy_stage_id", "target_subsidy_stage_id",
                                         "target_application_status", "condition", "send_message", "clone_data")
values ('fc076d68-f51a-4aa7-b190-be0c584d0fca', 'Aanvulling gevraagd', '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c',
        '721c1c28-e674-415f-b1c3-872a631ed045', 'requestForChanges',
        '{"type":"comparison","stage":2,"fieldCode":"firstAssessment","operator":"===","value":"Aanvulling nodig"}', true,
        true);

insert into public.subsidy_stage_transitions ("id", "description", "current_subsidy_stage_id", "target_subsidy_stage_id",
                                         "condition", "send_message")
values ('78014bba-1b91-4417-a8b7-cc97014487c8', 'Eerste beoordeling voltooid', '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c',
        'b2b08566-8493-4560-8afa-d56402931f74',
        '{"type":"in","stage":2,"fieldCode":"firstAssessment","values":["Goedgekeurd","Afgekeurd"]}', false);

insert into public.subsidy_stage_transitions ("id", "description", "current_subsidy_stage_id", "target_subsidy_stage_id",
                                         "condition", "send_message", "assign_to_previous_assessor", "clone_data")
values ('79a4eb8b-d42e-4f49-8f96-ff3433fb75c0', 'Tweede beoordeling oneens met eerste beoordeling',
        'b2b08566-8493-4560-8afa-d56402931f74', '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c',
        '{"type":"comparison","stage":3,"fieldCode":"secondAssessment","operator":"===","value":"Oneens met de eerste beoordeling"}',
        false, true, true);

insert into public.subsidy_stage_transitions ("id", "description", "current_subsidy_stage_id", "target_subsidy_stage_id",
                                         "target_application_status", "condition", "send_message")
values ('602b66cf-9062-4191-b00f-9530f6a3f87a', 'Tweede beoordeling eens met afkeuring eerste beoordeling',
        'b2b08566-8493-4560-8afa-d56402931f74', null, 'rejected',
        '{"type":"and","conditions":[{"type":"comparison","stage":2,"fieldCode":"firstAssessment","operator":"===","value":"Afgekeurd"},{"type":"comparison","stage":3,"fieldCode":"secondAssessment","operator":"===","value":"Eens met de eerste beoordeling"}]}',
        true);

insert into public.subsidy_stage_transitions ("id", "description", "current_subsidy_stage_id", "target_subsidy_stage_id",
                                         "condition", "send_message")
values ('03c4d9ba-6b82-42da-9ac2-2504f9319a91', 'Tweede beoordeling eens met goedkeuring eerste beoordeling',
        'b2b08566-8493-4560-8afa-d56402931f74', 'e456e790-1919-4a2b-b3d5-337d0053abe3',
        '{"type":"and","conditions":[{"type":"comparison","stage":2,"fieldCode":"firstAssessment","operator":"===","value":"Goedgekeurd"},{"type":"comparison","stage":3,"fieldCode":"secondAssessment","operator":"===","value":"Eens met de eerste beoordeling"}]}',
        false);

insert into public.subsidy_stage_transitions ("id", "description", "current_subsidy_stage_id", "target_subsidy_stage_id",
                                         "target_application_status", "condition", "send_message")
values ('3a293e03-1de4-47bf-917b-841b7c0a1fff', 'Aanvraag afgekeurd', 'e456e790-1919-4a2b-b3d5-337d0053abe3', null,
        'rejected',
        '{"type":"comparison","stage":4,"fieldCode":"internalAssessment","operator":"===","value":"Afgekeurd"}', true);

insert into public.subsidy_stage_transitions ("id", "description", "current_subsidy_stage_id", "target_subsidy_stage_id",
                                         "target_application_status", "condition", "send_message")
values ('5b876216-ba37-4b13-aa99-e311db027d6b', 'Aanvraag goedgekeurd', 'e456e790-1919-4a2b-b3d5-337d0053abe3', null,
        'approved',
        '{"type":"comparison","stage":4,"fieldCode":"internalAssessment","operator":"===","value":"Goedgekeurd"}', true);

insert into public.subsidy_stage_transition_messages ("id", "subsidy_stage_transition_id", "version", "status", "created_at",
                                                 "subject", "content_pdf", "content_html")
values ('cffe3600-77a9-43b2-9882-7b7f56c4d0ad', 'fc076d68-f51a-4aa7-b190-be0c584d0fca', 1, 'published',
        '2024-01-05 14:06:38', 'Aanvulling nodig', '{layout ''letter_layout.latte''}

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
        Als de gevraagde gegevens niet binnen 2 weken door mij zijn ontvangen, of onvoldoende zijn voor verdere
        beoordeling, dan kan uw aanvraag niet verder worden behandeld.
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
        Als de gevraagde gegevens niet binnen 2 weken door mij zijn ontvangen, of onvoldoende zijn voor verdere
        beoordeling, dan kan uw aanvraag niet verder worden behandeld.
    </p>
    <p>&nbsp;</p>
{/block}
');

insert into public.subsidy_stage_transition_messages ("id", "subsidy_stage_transition_id", "version", "status", "created_at",
                                                 "subject", "content_pdf", "content_html")
values ('c3b32e69-e093-4f0f-9318-7cc771114f2d', '602b66cf-9062-4191-b00f-9530f6a3f87a', 1, 'published',
        '2024-01-05 14:06:38', 'Aanvraag afgekeurd', '{layout ''letter_layout.latte''}

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

insert into public.subsidy_stage_transition_messages ("id", "subsidy_stage_transition_id", "version", "status", "created_at",
                                                 "subject", "content_pdf", "content_html")
values ('7476a2bd-15eb-4ab8-be8e-c9f3dd07f9b7', '3a293e03-1de4-47bf-917b-841b7c0a1fff', 1, 'published',
        '2024-01-05 14:06:38', 'Aanvraag afgekeurd', '{layout ''letter_layout.latte''}

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

insert into public.subsidy_stage_transition_messages ("id", "subsidy_stage_transition_id", "version", "status", "created_at",
                                                 "subject", "content_pdf", "content_html")
values ('1983fa28-cfc6-4c0f-9bc3-cba9e0909456', '5b876216-ba37-4b13-aa99-e311db027d6b', 1, 'published',
        '2024-01-05 14:06:38', 'Aanvraag goedgekeurd', '{layout ''letter_layout.latte''}

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
        <p>Het bedrag van {$content->stage2->amount} wordt in één keer uitbetaald. Wij streven ernaar de financiële
            ondersteuning binnen 10 werkdagen uit te keren.</p>

{/block}
');

