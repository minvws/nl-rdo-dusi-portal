insert into public.subsidies ("id", "title", "reference_prefix", "code", "description", "valid_from", "valid_to")
values ('7b9f1318-4c38-4fe5-881b-074729d95abf', 'Subsidieregeling reiskosten DAMU-leerlingen', 'DAMU', 'DAMU',
        'Ouders of verzorgers van een DAMU-leerling op het primair of het voortgezet onderwijs kunnen een tegemoetkoming in de reiskosten aanvragen. Deze reiskosten kunnen namelijk een barrière zijn voor talentvolle leerlingen om een opleiding aan een DAMU-school te volgen.',
        'now()', null);

insert into public.subsidy_versions ("id", "subsidy_id", "version", "status", "created_at", "subsidy_page_url",
                                "contact_mail_address", "mail_to_address_field_identifier",
                                "mail_to_name_field_identifier", "review_period")
values ('9a362ac7-281e-404a-b458-bdfb24f80fb0', '7b9f1318-4c38-4fe5-881b-074729d95abf', 1, 'published', 'now()',
        'https://www.dus-i.nl/subsidies/reiskosten-damu-leerlingen-primair-onderwijs', 'damu.dus-i@minvws.nl', 'email',
        'firstName;infix;lastName', 91);

insert into public.subsidy_stages ("id", "subsidy_version_id", "title", "subject_role", "stage")
values ('77996a9c-5c8d-47e1-9a88-e41bf594cfc8', '9a362ac7-281e-404a-b458-bdfb24f80fb0', 'Aanvraag', 'applicant', 1);

insert into public.subsidy_stages ("id", "subsidy_version_id", "title", "subject_role", "assessor_user_role", "stage",
                              "internal_note_field_code")
values ('fb21ee98-9f58-40b1-9432-fad2937688dc', '9a362ac7-281e-404a-b458-bdfb24f80fb0', 'Eerste beoordeling',
        'assessor', 'assessor', 2, 'firstAssessmentInternalNote');

insert into public.subsidy_stages ("id", "subsidy_version_id", "title", "subject_role", "assessor_user_role", "stage",
                              "internal_note_field_code")
values ('f343892a-17a8-48e5-81b0-6c3cb710c29a', '9a362ac7-281e-404a-b458-bdfb24f80fb0',
        'Uitvoeringscoördinator controle', 'assessor', 'implementationCoordinator', 3,
        'implementationCoordinatorAssessmentInternalNote');

insert into public.subsidy_stages ("id", "subsidy_version_id", "title", "subject_role", "assessor_user_role", "stage",
                              "internal_note_field_code")
values ('f36ae9b6-1340-453f-8ca7-611bfe9b94cd', '9a362ac7-281e-404a-b458-bdfb24f80fb0', 'Interne controle', 'assessor',
        'internalAuditor', 4, 'internalAssessmentInternalNote');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('6cf5e3a4-b320-4064-942a-7fabc687a739', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'firstName', 'Voornaam', null,
        'text', 'null', true, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('5a8bd826-22f1-41c3-8189-2d2a4973b682', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'infix', 'Tussenvoegsel', null,
        'text', 'null', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('78856ae3-8543-478b-8638-be15fe511286', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'lastName', 'Achternaam', null,
        'text', 'null', true, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('bfde62ff-89e3-4b07-9265-710e7276f4bf', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'street', 'Straat', null,
        'text', 'null', true, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('7e0d655f-2220-48f3-bf7f-0b51f9b96761', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'houseNumber', 'Huisnummer',
        null, 'text:numeric', 'null', true, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('5379c8af-4286-4b37-9ee0-3552133e57ce', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'houseNumberSuffix',
        'Huisnummer toevoeging', null, 'text', '{
        "maxLength": 10
    }', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('c42b49b1-d1f6-47ff-b7c9-ef3949a9c1a3', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'postalCode', 'Postcode', null,
        'custom:postalcode', 'null', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('e874091a-3676-4791-bde1-b2927e03c05d', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'city', 'Plaats', null, 'text',
        '{
            "maxLength": 100
        }', true, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('e7b4141d-f10f-4f7d-808f-eedf858aaedf', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'country', 'Land', null,
        'select', '{
        "options": [
            "Afghanistan",
            "\u00c5land",
            "Albani\u00eb",
            "Algerije",
            "Amerikaanse Maagdeneilanden",
            "Amerikaans-Samoa",
            "Andorra",
            "Angola",
            "Anguilla",
            "Antarctica",
            "Antigua en Barbuda",
            "Argentini\u00eb",
            "Armeni\u00eb",
            "Aruba",
            "Australi\u00eb",
            "Azerbeidzjan",
            "Bahama\u2019s",
            "Bahrein",
            "Bangladesh",
            "Barbados",
            "Belgi\u00eb",
            "Belize",
            "Benin",
            "Bermuda",
            "Bhutan",
            "Bolivia",
            "Bosni\u00eb en Herzegovina",
            "Botswana",
            "Bouveteiland",
            "Brazili\u00eb",
            "Britse Maagdeneilanden",
            "Brits Indische Oceaanterritorium",
            "Brunei",
            "Bulgarije",
            "Burkina Faso",
            "Burundi",
            "Cambodja",
            "Canada",
            "Centraal-Afrikaanse Republiek",
            "Chili",
            "China",
            "Christmaseiland",
            "Cocoseilanden",
            "Colombia",
            "Comoren",
            "Congo-Brazzaville",
            "Congo-Kinshasa",
            "Cookeilanden",
            "Costa Rica",
            "Cuba",
            "Cura\u00e7ao",
            "Cyprus",
            "Denemarken",
            "Djibouti",
            "Dominica",
            "Dominicaanse Republiek",
            "Duitsland",
            "Ecuador",
            "Egypte",
            "El Salvador",
            "Equatoriaal-Guinea",
            "Eritrea",
            "Estland",
            "Ethiopi\u00eb",
            "Faer\u00f6er",
            "Falklandeilanden",
            "Fiji",
            "Filipijnen",
            "Finland",
            "Frankrijk",
            "Franse Zuidelijke en Antarctische Gebieden",
            "Frans-Guyana",
            "Frans-Polynesi\u00eb",
            "Gabon",
            "Gambia",
            "Georgi\u00eb",
            "Ghana",
            "Gibraltar",
            "Grenada",
            "Griekenland",
            "Groenland",
            "Guadeloupe",
            "Guam",
            "Guatemala",
            "Guernsey",
            "Guinee",
            "Guinee-Bissau",
            "Guyana",
            "Ha\u00efti",
            "Heard en McDonaldeilanden",
            "Honduras",
            "Hongarije",
            "Hongkong",
            "Ierland",
            "IJsland",
            "India",
            "Indonesi\u00eb",
            "Irak",
            "Iran",
            "Isra\u00ebl",
            "Itali\u00eb",
            "Ivoorkust",
            "Jamaica",
            "Japan",
            "Jemen",
            "Jersey",
            "Jordani\u00eb",
            "Kaaimaneilanden",
            "Kaapverdi\u00eb",
            "Kameroen",
            "Kazachstan",
            "Kenia",
            "Kirgizi\u00eb",
            "Kiribati",
            "Kleine Pacifische eilanden van de Verenigde Staten",
            "Koeweit",
            "Kroati\u00eb",
            "Laos",
            "Lesotho",
            "Letland",
            "Libanon",
            "Liberia",
            "Libi\u00eb",
            "Liechtenstein",
            "Litouwen",
            "Luxemburg",
            "Macau",
            "Madagaskar",
            "Malawi",
            "Maldiven",
            "Maleisi\u00eb",
            "Mali",
            "Malta",
            "Marokko",
            "Marshalleilanden",
            "Martinique",
            "Mauritani\u00eb",
            "Mauritius",
            "Mayotte",
            "Mexico",
            "Micronesia",
            "Moldavi\u00eb",
            "Monaco",
            "Mongoli\u00eb",
            "Montenegro",
            "Montserrat",
            "Mozambique",
            "Myanmar",
            "Namibi\u00eb",
            "Nauru",
            "Nederland",
            "Nepal",
            "Nicaragua",
            "Nieuw-Caledoni\u00eb",
            "Nieuw-Zeeland",
            "Niger",
            "Nigeria",
            "Niue",
            "Noordelijke Marianen",
            "Noord-Korea",
            "Noord-Macedoni\u00eb",
            "code Land",
            "Noorwegen",
            "Norfolk",
            "Oeganda",
            "Oekra\u00efne",
            "Oezbekistan",
            "Oman",
            "Oostenrijk",
            "Oost-Timor",
            "Pakistan",
            "Palau",
            "Palestina",
            "Panama",
            "Papoea-Nieuw-Guinea",
            "Paraguay",
            "Peru",
            "Pitcairneilanden",
            "Polen",
            "Portugal",
            "Puerto Rico",
            "Qatar",
            "R\u00e9union",
            "Roemeni\u00eb",
            "Rusland",
            "Rwanda",
            "Saint-Barth\u00e9lemy",
            "Saint Kitts en Nevis",
            "Saint Lucia",
            "Saint-Pierre en Miquelon",
            "Saint Vincent en de Grenadines",
            "Salomonseilanden",
            "Samoa",
            "San Marino",
            "Saoedi-Arabi\u00eb",
            "Sao Tom\u00e9 en Principe",
            "Senegal",
            "Servi\u00eb",
            "Seychellen",
            "Sierra Leone",
            "Singapore",
            "Sint-Helena, Ascension en Tristan da Cunha",
            "Sint-Maarten",
            "Sint Maarten",
            "Sloveni\u00eb",
            "Slowakije",
            "Soedan",
            "Somali\u00eb",
            "Spanje",
            "Spitsbergen en Jan Mayen",
            "Sri Lanka",
            "Suriname",
            "Swaziland",
            "Syri\u00eb",
            "Tadzjikistan",
            "Taiwan",
            "Tanzania",
            "Thailand",
            "Togo",
            "Tokelau",
            "Tonga",
            "Trinidad en Tobago",
            "Tsjaad",
            "Tsjechi\u00eb",
            "Tunesi\u00eb",
            "Turkije",
            "Turkmenistan",
            "Turks- en Caicoseilanden",
            "Tuvalu",
            "Uruguay",
            "Vanuatu",
            "Vaticaanstad",
            "Venezuela",
            "Verenigde Arabische Emiraten",
            "Verenigde Staten",
            "Verenigd Koninkrijk",
            "Vietnam",
            "Wallis en Futuna",
            "Westelijke Sahara",
            "Wit-Rusland",
            "Zambia",
            "Zimbabwe",
            "Zuid-Afrika",
            "Zuid-Georgia en de Zuidelijke Sandwicheilanden",
            "Zuid-Korea",
            "Zuid-Soedan",
            "Zweden",
            "Zwitserland"
        ],
        "default": "Nederland"
    }', true, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('3e7340d1-909e-42d4-b27a-693d7f9de2bb', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'phoneNumber', 'Telefoonnummer',
        null, 'text:tel', '{
        "maxLength": 20
    }', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('95a34679-caae-4c8d-97e0-675955108f4c', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'email', 'E-mailadres', null,
        'text:email', '{
        "maxLength": 300
    }', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('94808852-59ee-4d4c-85eb-37eb259a1b4a', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'bankAccountNumber', 'IBAN',
        null, 'custom:bankaccount', 'null', true, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('eddca14f-a327-44cb-ab5e-3d988a6f680d', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'bankAccountHolder',
        'Naam rekeninghouder', null, 'text', '{
        "maxLength": 50
    }', true, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('27832989-87d4-4d17-a641-ba7b0e8d9865', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'isSingleParentFamily',
        'Is er sprake van een eenouder gezin?', null, 'select', '{
        "options": [
            "Ja",
            "Nee"
        ],
        "default": null
    }', true, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('974903c7-bd1d-4620-a9fa-a23ea39b1c0b', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'hasAlimony', 'Alimentatie?',
        null, 'select', '{
        "options": [
            "Ja",
            "Nee"
        ],
        "default": null
    }', true, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('6705a45f-7e34-4a0b-903b-d24394af8979', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'alimonyAmount',
        'Alimentatiebedrag', null, 'text:numeric', 'null', false, '{
        "type": "comparison",
        "stage": 1,
        "fieldCode": "hasAlimony",
        "operator": "===",
        "value": "Ja"
    }', 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('61f301c4-b688-4125-9d21-83e3b627c70c', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'annualIncomeParentA',
        'Jaarinkomen ouder 1', null, 'text:numeric', 'null', true, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('407a8e17-8a6c-405b-ada4-b24c39ccffde', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'annualIncomeParentB',
        'Jaarinkomen ouder 2', null, 'text:numeric', 'null', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('798b1a52-b4a2-4e5f-9b11-69d0a28241c8', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'annualJointIncome',
        'Jaarinkomen totaal', null, 'text:numeric', '{
        "readonly": true
    }', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('a9ba4d27-4e80-4d0d-8b9d-c8c75b416dd5', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'childName', 'Naam kind', null,
        'text', 'null', true, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('f1ff6fc0-114b-4acf-b62f-9902643f7223', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'dateOfBirth', 'Geboortedatum',
        null, 'date', 'null', true, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('398374ed-ad2b-4bbe-b242-0602aaba93dc', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'residentialStreet', 'Straat',
        null, 'text', 'null', true, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('e933bc00-9bdc-4e14-b574-e67b91af08aa', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'residentialHouseNumber',
        'Huisnummer', null, 'text:numeric', 'null', true, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('50291c74-25f6-4cbd-87d6-5e97c03770cc', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'residentialHouseNumberSuffix',
        'Huisnummer toevoeging', null, 'text', '{
        "maxLength": 10
    }', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('0fe7bf67-92f2-4cdb-8287-0e020d071b69', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'residentialPostalCode',
        'Postcode', null, 'custom:postalcode', 'null', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('b771f21c-9611-4028-854a-8779c6729641', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'residentialCity', 'Plaats',
        null, 'text', '{
        "maxLength": 100
    }', true, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('b67da2aa-256c-460a-9a84-a87a824404ba', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'educationType',
        'Gaat naar het:', null, 'select', '{
        "options": [
            "Primair onderwijs",
            "Voortgezet onderwijs"
        ],
        "default": null
    }', true, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('e01a06e3-3f57-4905-9592-08dc9e25703c', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'damuSchoolPrimary',
        'DAMU school', null, 'select', '{
        "options": [
            "Amsterdam - Olympiaschool",
            "Den Haag - School voor jong talent",
            "Rotterdam - Nieuwe Park Rozenburgschool"
        ],
        "default": null
    }', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('fdbf96fd-5001-41f1-8928-5f4942da246d', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'damuSchoolSecondary',
        'DAMU school', null, 'select', '{
        "options": [
            "Amsterdam - Gerrit van der Veen College",
            "Amsterdam - Individueel Voortgezet Kunstzinnig Onderwijs (IVKO)",
            "Arnhem - Beekdal Lyceum",
            "Den Haag - Interfaculteit School voor jong talent",
            "Enschede - Het Stedelijk Lyceum, locatie Kottenpark",
            "Haren - Zernike College",
            "Maastricht - Bonnefanten College",
            "Rotterdam - Havo\/Vwo voor muziek en dans",
            "Rotterdam - Thorbecke Voortgezet Onderwijs",
            "Tilburg - Koning Willem II College",
            "Venlo - Valuas College"
        ],
        "default": null
    }', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('692d7690-a678-40d7-8737-4adb2aef0e8b', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'travelDistanceSingleTrip',
        'Reisafstand enkele reis', null, 'text:float', '{
        "minimum": 1,
        "maximum": 9999
    }', true, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('82bfd7e4-68d0-4055-981c-5afca56f6bb7', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'totalDistance',
        'Totaal aantal kilometers', null, 'text:float', '{
        "readonly": true
    }', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('fbd86ae8-6479-4bb8-87ac-a5774b382827', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'travelExpenseReimbursement',
        'Kilometervergoeding', null, 'text:float', '{
        "readonly": true
    }', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('23782ee2-eb40-4e12-80eb-0b7801d0eda8', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'requestedSubsidyAmount',
        'Gevraagde subsidie bedrag', null, 'text:float', '{
        "readonly": true
    }', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('9d89a88f-b09f-4514-9274-51091e315372', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'IBDocument', 'IB60 formulier',
        null, 'upload', '{
        "mimeTypes": [
            "image\/jpeg",
            "image\/png",
            "application\/pdf"
        ],
        "maxFileSize": 20971520,
        "minItems": 1,
        "maxItems": 20
    }', true, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('c0a88576-2ebc-4e4d-b7d4-c4a8bec76b89', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'ANWBRouteCard',
        'ANWB routeplanner', null, 'upload', '{
        "mimeTypes": [
            "image\/jpeg",
            "image\/png",
            "application\/pdf"
        ],
        "maxFileSize": 20971520,
        "minItems": 1,
        "maxItems": 20
    }', true, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('e1a83b61-ee25-44b2-b1af-85d6b654a38e', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'proofOfRegistrationDAMUSchool',
        'Inschrijfbewijs DAMU school', null, 'upload', '{
        "mimeTypes": [
            "image\/jpeg",
            "image\/png",
            "application\/pdf"
        ],
        "maxFileSize": 20971520,
        "minItems": 1,
        "maxItems": 20
    }', true, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('23699851-c98f-4fe1-b7ff-92dd8814b9c5', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8',
        'proofOfRegistrationRegularSchool', 'Inschrijfbewijs reguliere school', null, 'upload', '{
        "mimeTypes": [
            "image\/jpeg",
            "image\/png",
            "application\/pdf"
        ],
        "maxFileSize": 20971520,
        "minItems": 1,
        "maxItems": 20
    }', true, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('5b5cd054-9410-4a4f-9470-ac44046101d1', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'truthfullyCompleted', '', null,
        'checkbox', 'null', true, null, 'short', true);

insert into public.subsidy_stage_hashes ("id", "subsidy_stage_id", "name", "description", "created_at", "updated_at")
values ('7f16ecd5-0450-4424-87d2-6626ee3bccda', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'Bank account',
        'Bank account duplicate reporting', '2024-03-11 14:17:20', '2024-03-11 14:17:20');

insert into public.subsidy_stage_hash_fields ("subsidy_stage_hash_id", "field_id")
values ('7f16ecd5-0450-4424-87d2-6626ee3bccda', (select id
                                                 from public.fields
                                                 where "subsidy_stage_id" = '77996a9c-5c8d-47e1-9a88-e41bf594cfc8'
                                                   and "code" = 'bankAccountNumber'
                                                   and "title" = 'IBAN'
                                                 limit 1));

insert into public.subsidy_stage_uis ("id", "subsidy_stage_id", "version", "status", "input_ui", "view_ui")
values ('bc406dd2-c425-4577-bb8e-b72453cae5bd', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 1, 'published', '{
    "type": "CustomPageNavigationControl",
    "elements": [
        {
            "type": "CustomPageControl",
            "label": "start",
            "elements": [
                {
                    "type": "FormGroupControl",
                    "options": {
                        "section": true
                    },
                    "elements": [
                        {
                            "type": "FormHtml",
                            "options": {
                                "html": "<p class=\"warning\">\n    <span>Waarschuwing:<\/span>\n    Het invullen van een aanvraag kost ongeveer 10 minuten. U kunt uw aanvraag tussentijds opslaan. Zorg ervoor dat u\n    alle gevraagde documenten digitaal bij de hand heeft. Dit kan bijvoorbeeld een scan, schermafdruk of foto vanaf uw\n    mobiele telefoon zijn. Laat uw aanvraag niet langer dan noodzakelijk open staan op uw computer.\n<\/p>\n\n<h2>Introductie<\/h2>\n<p>\n    Ouders of verzorgers van een DAMU-leerling op het primair of het voortgezet onderwijs kunnen een tegemoetkoming in\n    de reiskosten aanvragen. Deze reiskosten kunnen namelijk een barri\u00e8re zijn voor talentvolle leerlingen om een\n    opleiding aan een DAMU-school te volgen.\n<\/p>\n<p>\n    DAMU staat voor Dans en Muziek. De leerling moet zijn ingeschreven op een DAMU-school \u00e9n bij een hbo-vooropleiding\n    dans of muziek.\n<\/p>\n<h2>Belangrijke voorwaarden<\/h2>\n<p><\/p>\n<ul>\n    <li>\n        De scholier moet ingeschreven staan op een DAMU-school.\n    <\/li>\n    <li>De scholier moet ingeschreven staan bij een hbo-vooropleiding Dans of Muziek.<\/li>\n    <li>De reisafstand tussen het woonadres en de DAMU-school voor een enkele reis is minimaal 25 kilometer voor het\n        primair onderwijs of 20 kilometer voor het voorgezet onderwijs.\n    <\/li>\n    <li>De ouders of wettelijke vertegenwoordigers hebben een gezamenlijk jaarinkomen van niet meer dan \u20ac65.000.<\/li>\n<\/ul>\n<p>Laat uw aanvraag niet langer dan noodzakelijk open staan op uw computer.<\/p>\n<h2>Aanvraag starten<\/h2>\n"
                            }
                        }
                    ]
                }
            ],
            "options": {
                "required": [],
                "allOf": []
            }
        },
        {
            "type": "CustomPageControl",
            "label": "Persoonsgegevens toevoegen",
            "elements": [
                {
                    "type": "FormGroupControl",
                    "options": {
                        "section": true
                    },
                    "elements": [
                        {
                            "type": "Group",
                            "label": "Persoonlijke informatie aanvrager",
                            "elements": [
                                {
                                    "type": "VerticalLayout",
                                    "elements": [
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/firstName",
                                            "label": "Voornaam",
                                            "options": {
                                                "placeholder": ""
                                            }
                                        },
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/infix",
                                            "label": "Tussenvoegsel",
                                            "options": {
                                                "placeholder": ""
                                            }
                                        },
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/lastName",
                                            "label": "Achternaam",
                                            "options": {
                                                "placeholder": ""
                                            }
                                        }
                                    ]
                                }
                            ]
                        },
                        {
                            "type": "Group",
                            "label": "Adres",
                            "elements": [
                                {
                                    "type": "VerticalLayout",
                                    "elements": [
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/street",
                                            "label": "Straatnaam",
                                            "options": {
                                                "placeholder": ""
                                            }
                                        },
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/houseNumber",
                                            "label": "Huisnummer",
                                            "options": {
                                                "placeholder": ""
                                            }
                                        },
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/houseNumberSuffix",
                                            "label": "Huisnummer toevoeging",
                                            "options": {
                                                "placeholder": ""
                                            }
                                        },
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/postalCode",
                                            "label": "Postcode",
                                            "options": {
                                                "placeholder": ""
                                            }
                                        },
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/city",
                                            "label": "Plaatsnaam",
                                            "options": {
                                                "placeholder": ""
                                            }
                                        },
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/country",
                                            "label": "Land",
                                            "options": {
                                                "format": "select",
                                                "placeholder": "Selecteer een land"
                                            }
                                        }
                                    ]
                                }
                            ]
                        },
                        {
                            "type": "Group",
                            "label": "Contact",
                            "elements": [
                                {
                                    "type": "VerticalLayout",
                                    "elements": [
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/phoneNumber",
                                            "label": "Telefoonnummer",
                                            "options": {
                                                "placeholder": ""
                                            }
                                        },
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/email",
                                            "label": "E-mailadres",
                                            "options": {
                                                "placeholder": "",
                                                "tip": "U wordt via dit e-mailadres ge\u00efnformeerd over de status van uw aanvraag. Geef daarom alleen uw eigen e-mailadres door.",
                                                "validation": [
                                                    "onBlur"
                                                ]
                                            }
                                        }
                                    ]
                                }
                            ]
                        },
                        {
                            "type": "Group",
                            "label": "Bank",
                            "elements": [
                                {
                                    "type": "VerticalLayout",
                                    "elements": [
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/bankAccountHolder",
                                            "label": "Naam rekeninghouder",
                                            "options": {
                                                "placeholder": "",
                                                "validation": [
                                                    "onBlur"
                                                ]
                                            }
                                        },
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/bankAccountNumber",
                                            "label": "IBAN",
                                            "options": {
                                                "placeholder": "",
                                                "tip": "Staat u onder bewind? Vermeld dan het IBAN van uw beheerrekening.",
                                                "validation": [
                                                    "onValid"
                                                ]
                                            }
                                        }
                                    ]
                                }
                            ]
                        },
                        {
                            "type": "Group",
                            "label": "Inkomen",
                            "elements": [
                                {
                                    "type": "VerticalLayout",
                                    "elements": [
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/isSingleParentFamily",
                                            "label": "Is er sprake van een eenouder gezin?",
                                            "options": {
                                                "format": "radio",
                                                "placeholder": ""
                                            }
                                        },
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/hasAlimony",
                                            "label": "Alimentatie",
                                            "options": {
                                                "format": "radio",
                                                "placeholder": "",
                                                "remoteAction": [
                                                    "onChange"
                                                ]
                                            }
                                        },
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/alimonyAmount",
                                            "label": "Alimentatiebedrag",
                                            "options": {
                                                "placeholder": "",
                                                "remoteAction": [
                                                    "onBlur"
                                                ]
                                            },
                                            "rule": {
                                                "effect": "SHOW",
                                                "condition": {
                                                    "scope": "#\/properties\/hasAlimony",
                                                    "schema": {
                                                        "const": "Ja"
                                                    }
                                                }
                                            }
                                        },
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/annualIncomeParentA",
                                            "label": "Jaarinkomen ouder 1",
                                            "options": {
                                                "placeholder": "",
                                                "remoteAction": [
                                                    "onBlur"
                                                ]
                                            }
                                        },
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/annualIncomeParentB",
                                            "label": "Jaarinkomen ouder 2",
                                            "options": {
                                                "placeholder": "",
                                                "remoteAction": [
                                                    "onBlur"
                                                ]
                                            }
                                        },
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/annualJointIncome",
                                            "label": "Jaarinkomen totaal",
                                            "options": {
                                                "readonly": true
                                            }
                                        }
                                    ]
                                }
                            ]
                        },
                        {
                            "type": "Group",
                            "label": "Gegevens kind",
                            "elements": [
                                {
                                    "type": "VerticalLayout",
                                    "elements": [
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/childName",
                                            "label": "Naam kind",
                                            "options": {
                                                "placeholder": ""
                                            }
                                        },
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/dateOfBirth",
                                            "label": "Geboortedatum",
                                            "options": {
                                                "placeholder": ""
                                            }
                                        },
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/residentialStreet",
                                            "label": "Straatnaam",
                                            "options": {
                                                "placeholder": ""
                                            }
                                        },
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/residentialHouseNumber",
                                            "label": "Huisnummer",
                                            "options": {
                                                "placeholder": ""
                                            }
                                        },
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/residentialHouseNumberSuffix",
                                            "label": "Huisnummer toevoeging",
                                            "options": {
                                                "placeholder": ""
                                            }
                                        },
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/residentialPostalCode",
                                            "label": "Postcode",
                                            "options": {
                                                "placeholder": ""
                                            }
                                        },
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/residentialCity",
                                            "label": "Plaatsnaam",
                                            "options": {
                                                "placeholder": ""
                                            }
                                        },
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/educationType",
                                            "label": "Gaat naar het",
                                            "options": {
                                                "format": "radio",
                                                "placeholder": "",
                                                "remoteAction": [
                                                    "onBlur"
                                                ]
                                            }
                                        },
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/damuSchoolPrimary",
                                            "label": "DAMU school",
                                            "options": {
                                                "format": "select",
                                                "placeholder": ""
                                            },
                                            "rule": {
                                                "effect": "SHOW",
                                                "condition": {
                                                    "scope": "#\/properties\/educationType",
                                                    "schema": {
                                                        "const": "Primair onderwijs"
                                                    }
                                                }
                                            }
                                        },
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/damuSchoolSecondary",
                                            "label": "DAMU school",
                                            "options": {
                                                "format": "select",
                                                "placeholder": ""
                                            },
                                            "rule": {
                                                "effect": "SHOW",
                                                "condition": {
                                                    "scope": "#\/properties\/educationType",
                                                    "schema": {
                                                        "const": "Voortgezet onderwijs"
                                                    }
                                                }
                                            }
                                        }
                                    ]
                                }
                            ]
                        },
                        {
                            "type": "Group",
                            "label": "Subsidie",
                            "elements": [
                                {
                                    "type": "VerticalLayout",
                                    "elements": [
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/travelDistanceSingleTrip",
                                            "label": "Reisafstand enkele reis",
                                            "options": {
                                                "placeholder": "",
                                                "remoteAction": [
                                                    "onBlur"
                                                ]
                                            }
                                        },
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/totalDistance",
                                            "label": "Totaal aantal kilometers",
                                            "options": {
                                                "readonly": true,
                                                "placeholder": ""
                                            }
                                        },
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/travelExpenseReimbursement",
                                            "label": "Kilometervergoeding",
                                            "options": {
                                                "readonly": true,
                                                "placeholder": ""
                                            }
                                        },
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/requestedSubsidyAmount",
                                            "label": "Gevraagde subsidiebedrag",
                                            "options": {
                                                "readonly": true,
                                                "placeholder": ""
                                            }
                                        }
                                    ]
                                }
                            ]
                        }
                    ]
                }
            ],
            "options": {
                "required": [
                    "firstName",
                    "lastName",
                    "street",
                    "houseNumber",
                    "postalCode",
                    "city",
                    "country",
                    "phoneNumber",
                    "email",
                    "bankAccountHolder",
                    "bankAccountNumber",
                    "isSingleParentFamily",
                    "hasAlimony",
                    "annualIncomeParentA",
                    "childName",
                    "dateOfBirth",
                    "residentialStreet",
                    "residentialHouseNumber",
                    "residentialHouseNumberSuffix",
                    "residentialPostalCode",
                    "residentialCity",
                    "educationType",
                    "travelDistanceSingleTrip"
                ],
                "allOf": [
                    {
                        "if": {
                            "properties": {
                                "hasAlimony": {
                                    "const": "Ja"
                                }
                            }
                        },
                        "then": {
                            "required": [
                                "alimonyAmount"
                            ]
                        }
                    }
                ]
            }
        },
        {
            "type": "CustomPageControl",
            "label": "Documenten toevoegen",
            "elements": [
                {
                    "type": "FormGroupControl",
                    "options": {
                        "section": true
                    },
                    "elements": [
                        {
                            "type": "Group",
                            "label": "Documenten",
                            "elements": [
                                {
                                    "type": "VerticalLayout",
                                    "elements": [
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/IBDocument",
                                            "label": "IB60 formulier",
                                            "options": {
                                                "accept": "image\/jpeg,image\/png,.pdf",
                                                "maxFileSize": 5242880,
                                                "minItems": 1,
                                                "maxItems": 20,
                                                "tip": "In het formulier wordt het bruto jaarinkomen gevraagd van beide ouders of wettelijk vertegenwoordigers. Ook als u niet duurzaam samenleeft. Bij een eenoudergezin geldt dit uiteraard niet.<br\/><\/br\/>De inkomensverklaring kunt u downloaden in Mijn Belastingdienst onder het tabblad Inkomstenbelasting. U kunt de verklaring ook gratis aanvragen bij de Belastingdienst via het telefoonnummer 0800 0543."
                                            }
                                        }
                                    ]
                                },
                                {
                                    "type": "VerticalLayout",
                                    "elements": [
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/ANWBRouteCard",
                                            "label": "ANWB routeplanner",
                                            "options": {
                                                "accept": "image\/jpeg,image\/png,.pdf",
                                                "maxFileSize": 5242880,
                                                "minItems": 1,
                                                "maxItems": 20,
                                                "tip": "Gebruik de ANWB-routeplanner met de optie \u2018snelste route\u2019. U mag geen andere routeplanner gebruiken. Kies als route het woonadres naar de Damu-school. Schakel de optie ''Route op basis van actueel verkeer'' uit en klik op ''Herbereken route''."
                                            }
                                        }
                                    ]
                                },
                                {
                                    "type": "VerticalLayout",
                                    "elements": [
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/proofOfRegistrationDAMUSchool",
                                            "label": "Inschrijfbewijs DAMU school",
                                            "options": {
                                                "accept": "image\/jpeg,image\/png,.pdf",
                                                "maxFileSize": 5242880,
                                                "minItems": 1,
                                                "maxItems": 20,
                                                "tip": ""
                                            }
                                        }
                                    ]
                                },
                                {
                                    "type": "VerticalLayout",
                                    "elements": [
                                        {
                                            "type": "CustomControl",
                                            "scope": "#\/properties\/proofOfRegistrationRegularSchool",
                                            "label": "Inschrijfbewijs reguliere school",
                                            "options": {
                                                "accept": "image\/jpeg,image\/png,.pdf",
                                                "maxFileSize": 5242880,
                                                "minItems": 1,
                                                "maxItems": 20,
                                                "tip": ""
                                            }
                                        }
                                    ]
                                }
                            ]
                        }
                    ]
                }
            ],
            "options": {
                "required": [
                    "IBDocument",
                    "ANWBRouteCard",
                    "proofOfRegistrationDAMUSchool",
                    "proofOfRegistrationRegularSchool"
                ],
                "allOf": []
            }
        },
        {
            "type": "CustomPageControl",
            "label": "Controleren en ondertekenen",
            "elements": [
                {
                    "type": "FormGroupControl",
                    "options": {
                        "section": true
                    },
                    "elements": [
                        {
                            "type": "Group",
                            "label": "Controleren",
                            "elements": [
                                {
                                    "type": "FormResultsTable",
                                    "label": "Uw gegevens",
                                    "options": {
                                        "fields": {
                                            "Naam": "{firstName} {infix} {lastName}",
                                            "Adres": "{street} {houseNumber}{houseNumberSuffix} {postalCode} {city}",
                                            "Telefoon": "{phoneNumber}",
                                            "E-mailadres": "{email}"
                                        }
                                    }
                                }
                            ]
                        },
                        {
                            "type": "Group",
                            "label": "Ondertekenen",
                            "elements": [
                                {
                                    "type": "CustomControl",
                                    "scope": "#\/properties\/truthfullyCompleted",
                                    "label": "Inhoud",
                                    "options": {
                                        "description": "Ik verklaar het formulier naar waarheid te hebben ingevuld."
                                    }
                                }
                            ]
                        }
                    ]
                }
            ],
            "options": {
                "required": [
                    "truthfullyCompleted"
                ],
                "allOf": []
            }
        }
    ]
}', '{
    "type": "FormGroupControl",
    "options": {
        "section": true
    },
    "elements": [
        {
            "type": "FormGroupControl",
            "label": "Persoonlijke informatie",
            "options": {
                "section": true,
                "headingLevel": "2"
            },
            "elements": [
                {
                    "type": "FormResultsTable",
                    "options": {
                        "fields": {
                            "Voornaam": "{firstName}",
                            "Tussenvoegsel": "{infix}",
                            "Achternaam": "{lastName}"
                        }
                    }
                }
            ]
        },
        {
            "type": "FormGroupControl",
            "label": "Adres",
            "options": {
                "section": true,
                "headingLevel": "2"
            },
            "elements": [
                {
                    "type": "FormResultsTable",
                    "options": {
                        "fields": {
                            "Land": "{country}",
                            "Straatnaam": "{street}",
                            "Huisnummer": "{houseNumber}",
                            "Huisnummer toevoeging": "{houseNumberSuffix}",
                            "Postcode": "{postalCode}",
                            "Plaatsnaam": "{city}"
                        }
                    }
                }
            ]
        },
        {
            "type": "FormGroupControl",
            "label": "Contact",
            "options": {
                "section": true,
                "headingLevel": "2"
            },
            "elements": [
                {
                    "type": "FormResultsTable",
                    "options": {
                        "fields": {
                            "Telefoonnummer": "{phoneNumber}",
                            "E-mailadres": "{email}"
                        }
                    }
                }
            ]
        },
        {
            "type": "FormGroupControl",
            "label": "Bank",
            "options": {
                "section": true,
                "headingLevel": "2"
            },
            "elements": [
                {
                    "type": "FormResultsTable",
                    "options": {
                        "fields": {
                            "IBAN": "{bankAccountNumber}",
                            "Naam rekeninghouder": "{bankAccountHolder}"
                        }
                    }
                }
            ]
        },
        {
            "type": "FormGroupControl",
            "label": "Inkomen",
            "options": {
                "section": true,
                "headingLevel": "2"
            },
            "elements": [
                {
                    "type": "FormResultsTable",
                    "options": {
                        "fields": {
                            "Is er sprake van een eenoudergezin": "{isSingleParentFamily}",
                            "Alimentatie": "{hasAlimony}",
                            "Alimentatiebedrag": "{alimonyAmount}",
                            "Jaarinkomen ouder 1": "{annualIncomeParentA}",
                            "Jaarinkomen ouder 2": "{annualIncomeParentB}",
                            "Jaarinkomen totaal": "{annualJointIncome}"
                        }
                    }
                }
            ]
        },
        {
            "type": "FormGroupControl",
            "label": "Gegevens kind",
            "options": {
                "section": true,
                "headingLevel": "2"
            },
            "elements": [
                {
                    "type": "FormResultsTable",
                    "options": {
                        "fields": {
                            "Naam": "{childName}",
                            "Straat": "{residentialStreet}",
                            "Huisnummer": "{residentialHouseNumber}",
                            "Huisnummer toevoeging": "{residentialHouseNumberSuffix}",
                            "Postcode": "{residentialPostalCode}",
                            "Plaatsnaam": "{residentialCity}",
                            "Gaat naar het": "{educationType}"
                        }
                    }
                },
                {
                    "type": "FormResultsTable",
                    "options": {
                        "fields": {
                            "Damu School primair onderwijs": "{damuSchoolPrimary}"
                        }
                    },
                    "rule": {
                        "effect": "SHOW",
                        "condition": {
                            "scope": "#\/properties\/educationType",
                            "schema": {
                                "const": "Primair onderwijs"
                            }
                        }
                    }
                },
                {
                    "type": "FormResultsTable",
                    "options": {
                        "fields": {
                            "Damu School primair onderwijs": "{damuSchoolSecondary}"
                        }
                    },
                    "rule": {
                        "effect": "SHOW",
                        "condition": {
                            "scope": "#\/properties\/educationType",
                            "schema": {
                                "const": "Voortgezet onderwijs"
                            }
                        }
                    }
                }
            ]
        },
        {
            "type": "FormGroupControl",
            "label": "Subsidie",
            "options": {
                "section": true,
                "headingLevel": "2"
            },
            "elements": [
                {
                    "type": "FormResultsTable",
                    "options": {
                        "fields": {
                            "Reisafstand enkele reis": "{travelDistanceSingleTrip}",
                            "Totaal aantal kilometers": "{totalDistance}",
                            "Kilometervergoeding": "{travelExpenseReimbursement}",
                            "Gevraagd subsidiebedrag": "{requestedSubsidyAmount}"
                        }
                    }
                }
            ]
        },
        {
            "type": "FormGroupControl",
            "label": "Bestanden",
            "options": {
                "section": true,
                "headingLevel": "2"
            },
            "elements": [
                {
                    "type": "FormResultsTable",
                    "options": {
                        "fields": {
                            "IB60 formulier": "{IBDocument}",
                            "ANWB routeplanner": "{ANWBRouteCard}",
                            "Inschrijfbewijs DAMU school": "{proofOfRegistrationDAMUSchool}",
                            "Inschrijfbewijs reguliere school": "{proofOfRegistrationRegularSchool}"
                        }
                    }
                }
            ]
        }
    ]
}');

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('74a49112-73d5-41bb-81fc-4fc8a4d9685f', 'fb21ee98-9f58-40b1-9432-fad2937688dc', 'firstAssessmentChecklist',
        'Gecontroleerd', null, 'multiselect', '{
        "options": [
            "Woont de aanvrager niet in Caribisch Nederland?",
            "Is het inschrijvingsbewijs bij de DAMU school aangeleverd?",
            "Is naam van de leerling op het inschrijvingsbewijs hetzelfde als waarvoor subsidie wordt aangevraagd?",
            "Is een recente inkomensverklaring (van beide ouders) aangeleverd (maximaal 2 kalenderjaren oud)?",
            "Zijn onnodige gegevens onleesbaar gemaakt?"
        ]
    }', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('41ca2751-c561-4978-966a-1f1be4cbc1c0', 'fb21ee98-9f58-40b1-9432-fad2937688dc', 'isMinimumTravelDistanceMet',
        'Is voldaan aan de minimale reisafstand tussen het woonadres en de DAMU school, volgens de ANWB routeplanner?',
        null, 'select', '{
        "options": [
            "Ja",
            "Nee"
        ],
        "default": null
    }', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('0cc7ae61-57bb-4afb-a40c-3629c0f37259', 'fb21ee98-9f58-40b1-9432-fad2937688dc',
        'actualTravelDistanceSingleTrip', 'Reisafstand volgens de ANWB routeplanner', null, 'text:float', '{
        "minimum": 1,
        "maximum": 9999
    }', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('e2cf871d-75eb-4bb1-be09-c9fe6c4ec675', 'fb21ee98-9f58-40b1-9432-fad2937688dc',
        'isSubmittedYearlyIncomeCorrect', 'Is het ingevulde gezamenlijk jaarinkomen correct?', null, 'select', '{
        "options": [
            "Ja",
            "Nee"
        ],
        "default": null
    }', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('c0b40389-8340-4c8f-bf46-7055dd63fc32', 'fb21ee98-9f58-40b1-9432-fad2937688dc', 'actualAnnualJointIncome',
        'Werkelijk gezamenlijk jaarinkomen', null, 'text:numeric', '{
        "minimum": 0
    }', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('ea80447d-9a9c-4f00-8d98-595ef9469867', 'fb21ee98-9f58-40b1-9432-fad2937688dc',
        'actualTravelExpenseReimbursement', 'Werkelijke klilometervergoeding', null, 'text:float', '{
        "readonly": true
    }', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('be4cfab2-fa24-459a-978b-049d8744d753', 'fb21ee98-9f58-40b1-9432-fad2937688dc', 'actualRequestedSubsidyAmount',
        'Werkelijjk aangevraagd subsidie bedrag', null, 'text:float', '{
        "readonly": true
    }', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('e897042d-a6cf-4b87-a226-e8397c3aca6d', 'fb21ee98-9f58-40b1-9432-fad2937688dc', 'businessPartnerNumber',
        'ZP-nummer', null, 'text:numeric', '{
        "minimum": 0
    }', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('2e4cd6ec-5377-41d4-b589-1b7f4a91c690', 'fb21ee98-9f58-40b1-9432-fad2937688dc', 'decisionCategory',
        'Soort beoordeling', null, 'select', '{
        "options": [
            "Toewijzing",
            "Afwijzing",
            "Bijstelling",
            "Hardheidsclausule"
        ],
        "default": null
    }', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('333adf07-2d9c-4bf4-9606-60865df31204', 'fb21ee98-9f58-40b1-9432-fad2937688dc', 'firstAssessment',
        'Beoordeling', null, 'select', '{
        "options": [
            "Aanvulling nodig",
            "Afgekeurd",
            "Goedgekeurd"
        ],
        "default": null
    }', true, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('fbd687f9-00d0-4311-a992-eabe1d54f753', 'fb21ee98-9f58-40b1-9432-fad2937688dc',
        'firstAssessmentRequestedComplementReason', 'Reden', null, 'select', '{
        "options": [
            "Incomplete aanvraag",
            "Onduidelijkheid of vervolgvragen"
        ],
        "default": null
    }', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('8cf138c5-adc3-4233-916b-93c23b025ffc', 'fb21ee98-9f58-40b1-9432-fad2937688dc',
        'firstAssessmentRequestedComplementNote', 'Toelichting van benodigde aanvullingen', null, 'text', 'null', false,
        null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('4fdeca5a-84e8-45e7-9446-64b244e1e6b9', 'fb21ee98-9f58-40b1-9432-fad2937688dc', 'firstAssessmentRejectedNote',
        'Reden van afkeuring', null, 'text', 'null', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('0b220756-3897-4f57-8f6f-d2b536e33727', 'fb21ee98-9f58-40b1-9432-fad2937688dc', 'firstAssessmentApprovedNote',
        'Motivatie van goedkeuring', null, 'text', 'null', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('aaa1267f-8388-440c-8ed6-0f5b8c02a104', 'fb21ee98-9f58-40b1-9432-fad2937688dc', 'firstAssessmentInternalNote',
        'Interne notitie', null, 'text', 'null', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('7f99d12c-b130-41ec-999f-c0ff44299c51', 'f36ae9b6-1340-453f-8ca7-611bfe9b94cd', 'internalAssessmentChecklist',
        'Gecontroleerd', null, 'multiselect', '{
        "options": [
            "Valt de aanvrager onder de WSNP\/bewindvoering?",
            "Is het subsidiebedrag juist vermeld in SAP?",
            "Is het in de brief opgenomen IBAN juist conform SAP en aanvraagformulier?",
            "Is de aangemaakte verplichting geboekt op juiste budgetplaats en budgetpositie?"
        ]
    }', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('bbe98e24-17aa-4be1-967f-77e6d83549a9', 'f36ae9b6-1340-453f-8ca7-611bfe9b94cd', 'subsidyObligationApproved',
        'Is de verplichting goedgekeurd?', null, 'select', '{
        "options": [
            "Ja",
            "Nee",
            "Nvt"
        ],
        "default": null
    }', true, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('e89bac6e-a984-4a8b-b673-87c5078134f8', 'f36ae9b6-1340-453f-8ca7-611bfe9b94cd', 'internalAssessment',
        'Beoordeling', null, 'select', '{
        "options": [
            "Eens met de eerste beoordeling",
            "Oneens met de eerste beoordeling"
        ],
        "default": null
    }', true, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('c639c4af-07bf-48c0-835d-64ce15759627', 'f36ae9b6-1340-453f-8ca7-611bfe9b94cd',
        'internalAssessmentInternalNote', 'Interne notitie', null, 'text', 'null', false, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('98fbc9aa-e346-4887-a6b5-f899908f8076', 'f343892a-17a8-48e5-81b0-6c3cb710c29a',
        'implementationCoordinatorAssessment', 'Beoordeling', null, 'select', '{
        "options": [
            "Eens met de eerste beoordeling",
            "Oneens met de eerste beoordeling"
        ],
        "default": null
    }', true, null, 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('506e9ab9-eb1c-485c-a129-8deeaad0c1f5', 'f343892a-17a8-48e5-81b0-6c3cb710c29a', 'amount', 'Bedrag', null,
        'text', 'null', false, '{
        "type": "and",
        "conditions": [
            {
                "type": "comparison",
                "stage": 2,
                "fieldCode": "firstAssessment",
                "operator": "===",
                "value": "Goedgekeurd"
            },
            {
                "type": "comparison",
                "stage": 3,
                "fieldCode": "implementationCoordinatorAssessment",
                "operator": "===",
                "value": "Eens met de eerste beoordeling"
            }
        ]
    }', 'short', false);

insert into public.fields ("id", "subsidy_stage_id", "code", "title", "description", "type", "params", "is_required",
                      "required_condition", "retention_period_on_approval", "exclude_from_clone_data")
values ('3c2ead63-9187-4c48-8304-4b7708317eef', 'f343892a-17a8-48e5-81b0-6c3cb710c29a',
        'implementationCoordinatorAssessmentInternalNote', 'Interne notitie', null, 'text', 'null', false, null, 'short',
        false);

insert into public.subsidy_stage_uis ("id", "subsidy_stage_id", "version", "status", "input_ui", "view_ui")
values ('2f307337-176e-4828-8e4c-0acb8125f420', 'fb21ee98-9f58-40b1-9432-fad2937688dc', 1, 'published', '{
    "type": "FormGroupControl",
    "options": {
        "section": true,
        "group": true
    },
    "elements": [
        {
            "type": "Group",
            "label": "Beoordeling",
            "elements": [
                {
                    "type": "VerticalLayout",
                    "elements": [
                        {
                            "type": "CustomControl",
                            "scope": "#\/properties\/firstAssessmentChecklist",
                            "options": {
                                "format": "checkbox-group"
                            }
                        },
                        {
                            "type": "CustomControl",
                            "scope": "#\/properties\/isMinimumTravelDistanceMet",
                            "options": {
                                "format": "radio"
                            }
                        },
                        {
                            "type": "CustomControl",
                            "scope": "#\/properties\/actualTravelDistanceSingleTrip",
                            "options": {
                                "remoteAction": [
                                    "onBlur"
                                ]
                            }
                        },
                        {
                            "type": "CustomControl",
                            "scope": "#\/properties\/isSubmittedYearlyIncomeCorrect",
                            "options": {
                                "format": "radio"
                            }
                        },
                        {
                            "type": "CustomControl",
                            "scope": "#\/properties\/actualAnnualJointIncome",
                            "options": {
                                "remoteAction": [
                                    "onBlur"
                                ]
                            }
                        },
                        {
                            "type": "CustomControl",
                            "scope": "#\/properties\/actualTravelExpenseReimbursement",
                            "options": {
                                "readonly": true
                            }
                        },
                        {
                            "type": "CustomControl",
                            "scope": "#\/properties\/actualRequestedSubsidyAmount",
                            "options": {
                                "readonly": true
                            }
                        },
                        {
                            "type": "CustomControl",
                            "scope": "#\/properties\/businessPartnerNumber",
                            "options": []
                        },
                        {
                            "type": "CustomControl",
                            "scope": "#\/properties\/decisionCategory",
                            "options": {
                                "format": "radio"
                            }
                        }
                    ]
                }
            ]
        },
        {
            "type": "Group",
            "label": "Status",
            "elements": [
                {
                    "type": "VerticalLayout",
                    "elements": [
                        {
                            "type": "CustomControl",
                            "scope": "#\/properties\/firstAssessment",
                            "options": {
                                "format": "radio"
                            }
                        }
                    ]
                }
            ]
        },
        {
            "type": "Group",
            "label": "Toelichting",
            "elements": [
                {
                    "type": "VerticalLayout",
                    "elements": [
                        {
                            "type": "CustomControl",
                            "scope": "#\/properties\/firstAssessmentRequestedComplementReason",
                            "options": {
                                "format": "radio"
                            },
                            "rule": {
                                "effect": "SHOW",
                                "condition": {
                                    "scope": "#\/properties\/firstAssessment",
                                    "schema": {
                                        "const": "Aanvulling nodig"
                                    }
                                }
                            }
                        }
                    ]
                },
                {
                    "type": "VerticalLayout",
                    "elements": [
                        {
                            "type": "CustomControl",
                            "scope": "#\/properties\/firstAssessmentRequestedComplementNote",
                            "options": {
                                "format": "textarea",
                                "tip": "Deze notitie wordt opgenomen binnen de brief aan de aanvrager."
                            },
                            "rule": {
                                "effect": "SHOW",
                                "condition": {
                                    "scope": "#\/properties\/firstAssessment",
                                    "schema": {
                                        "const": "Aanvulling nodig"
                                    }
                                }
                            }
                        }
                    ]
                },
                {
                    "type": "VerticalLayout",
                    "elements": [
                        {
                            "type": "CustomControl",
                            "scope": "#\/properties\/firstAssessmentRejectedNote",
                            "options": {
                                "format": "textarea",
                                "tip": "Deze notitie wordt opgenomen binnen de brief aan de aanvrager."
                            },
                            "rule": {
                                "effect": "SHOW",
                                "condition": {
                                    "scope": "#\/properties\/firstAssessment",
                                    "schema": {
                                        "const": "Afgekeurd"
                                    }
                                }
                            }
                        }
                    ]
                },
                {
                    "type": "VerticalLayout",
                    "elements": [
                        {
                            "type": "CustomControl",
                            "scope": "#\/properties\/firstAssessmentApprovedNote",
                            "options": {
                                "format": "textarea",
                                "tip": "Deze notitie wordt opgenomen binnen de brief aan de aanvrager."
                            },
                            "rule": {
                                "effect": "SHOW",
                                "condition": {
                                    "scope": "#\/properties\/firstAssessment",
                                    "schema": {
                                        "const": "Goedgekeurd"
                                    }
                                }
                            }
                        }
                    ]
                },
                {
                    "type": "VerticalLayout",
                    "elements": [
                        {
                            "type": "CustomControl",
                            "scope": "#\/properties\/firstAssessmentInternalNote",
                            "options": {
                                "format": "textarea"
                            }
                        }
                    ]
                }
            ]
        }
    ]
}', '{
    "type": "FormGroupControl",
    "options": {
        "section": true
    },
    "elements": [
        {
            "type": "FormGroupControl",
            "label": "Eerste beoordeling",
            "options": {
                "section": true,
                "headingLevel": "2"
            },
            "elements": [
                {
                    "type": "CustomControl",
                    "scope": "#\/properties\/firstAssessmentChecklist",
                    "options": {
                        "readonly": true,
                        "format": "checkbox-group"
                    }
                },
                {
                    "type": "CustomControl",
                    "scope": "#\/properties\/isMinimumTravelDistanceMet",
                    "options": {
                        "readonly": true,
                        "format": "radio"
                    }
                },
                {
                    "type": "CustomControl",
                    "scope": "#\/properties\/actualTravelDistanceSingleTrip",
                    "options": {
                        "readonly": true
                    }
                },
                {
                    "type": "CustomControl",
                    "scope": "#\/properties\/isSubmittedYearlyIncomeCorrect",
                    "options": {
                        "readonly": true,
                        "format": "radio"
                    }
                },
                {
                    "type": "CustomControl",
                    "scope": "#\/properties\/actualAnnualJointIncome",
                    "options": {
                        "readonly": true
                    }
                },
                {
                    "type": "CustomControl",
                    "scope": "#\/properties\/actualTravelExpenseReimbursement",
                    "options": {
                        "readonly": true
                    }
                },
                {
                    "type": "CustomControl",
                    "scope": "#\/properties\/actualRequestedSubsidyAmount",
                    "options": {
                        "readonly": true
                    }
                },
                {
                    "type": "CustomControl",
                    "scope": "#\/properties\/businessPartnerNumber",
                    "options": {
                        "readonly": true
                    }
                },
                {
                    "type": "CustomControl",
                    "scope": "#\/properties\/decisionCategory",
                    "options": {
                        "readonly": true,
                        "format": "radio"
                    }
                }
            ]
        },
        {
            "type": "FormGroupControl",
            "label": "Eerste beoordeling",
            "options": {
                "section": true,
                "headingLevel": "2"
            },
            "elements": [
                {
                    "type": "CustomControl",
                    "scope": "#\/properties\/firstAssessment",
                    "options": {
                        "readonly": true,
                        "format": "radio"
                    }
                },
                {
                    "type": "CustomControl",
                    "scope": "#\/properties\/firstAssessmentRequestedComplementReason",
                    "options": {
                        "readonly": true,
                        "format": "radio"
                    },
                    "rule": {
                        "effect": "SHOW",
                        "condition": {
                            "scope": "#\/properties\/firstAssessment",
                            "schema": {
                                "const": "Aanvulling nodig"
                            }
                        }
                    }
                },
                {
                    "type": "CustomControl",
                    "scope": "#\/properties\/firstAssessmentRequestedComplementNote",
                    "options": {
                        "readonly": true,
                        "format": "textarea"
                    },
                    "rule": {
                        "effect": "SHOW",
                        "condition": {
                            "scope": "#\/properties\/firstAssessment",
                            "schema": {
                                "const": "Aanvulling nodig"
                            }
                        }
                    }
                },
                {
                    "type": "CustomControl",
                    "scope": "#\/properties\/firstAssessmentRejectedNote",
                    "options": {
                        "readonly": true,
                        "format": "textarea"
                    },
                    "rule": {
                        "effect": "SHOW",
                        "condition": {
                            "scope": "#\/properties\/firstAssessment",
                            "schema": {
                                "const": "Afgekeurd"
                            }
                        }
                    }
                },
                {
                    "type": "CustomControl",
                    "scope": "#\/properties\/firstAssessmentApprovedNote",
                    "options": {
                        "readonly": true,
                        "format": "textarea"
                    },
                    "rule": {
                        "effect": "SHOW",
                        "condition": {
                            "scope": "#\/properties\/firstAssessment",
                            "schema": {
                                "const": "Goedgekeurd"
                            }
                        }
                    }
                },
                {
                    "type": "CustomControl",
                    "scope": "#\/properties\/firstAssessmentInternalNote",
                    "options": {
                        "readonly": true,
                        "format": "textarea"
                    }
                }
            ]
        }
    ]
}');

insert into public.subsidy_stage_uis ("id", "subsidy_stage_id", "version", "status", "input_ui", "view_ui")
values ('263c2c52-eaaa-40da-9209-9e84d2b78cd2', 'f36ae9b6-1340-453f-8ca7-611bfe9b94cd', 1, 'published', '{
    "type": "FormGroupControl",
    "options": {
        "section": true,
        "group": true
    },
    "elements": [
        {
            "type": "Group",
            "label": "Status",
            "elements": [
                {
                    "type": "VerticalLayout",
                    "elements": [
                        {
                            "type": "CustomControl",
                            "scope": "#\/properties\/internalAssessmentChecklist",
                            "options": {
                                "format": "checkbox-group"
                            }
                        },
                        {
                            "type": "CustomControl",
                            "scope": "#\/properties\/subsidyObligationApproved",
                            "options": {
                                "format": "radio"
                            }
                        }
                    ]
                },
                {
                    "type": "VerticalLayout",
                    "elements": [
                        {
                            "type": "CustomControl",
                            "scope": "#\/properties\/internalAssessment",
                            "options": {
                                "format": "radio"
                            }
                        }
                    ]
                },
                {
                    "type": "VerticalLayout",
                    "elements": [
                        {
                            "type": "CustomControl",
                            "scope": "#\/properties\/internalAssessmentInternalNote",
                            "options": {
                                "format": "textarea"
                            }
                        }
                    ]
                }
            ]
        }
    ]
}', '{
    "type": "FormGroupControl",
    "options": {
        "section": true
    },
    "elements": [
        {
            "type": "CustomControl",
            "scope": "#\/properties\/internalAssessmentChecklist",
            "options": {
                "readonly": true,
                "format": "checkbox-group"
            }
        },
        {
            "type": "CustomControl",
            "scope": "#\/properties\/subsidyObligationApproved",
            "options": {
                "readonly": true,
                "format": "radio"
            }
        },
        {
            "type": "CustomControl",
            "scope": "#\/properties\/internalAssessment",
            "options": {
                "readonly": true,
                "format": "radio"
            }
        },
        {
            "type": "CustomControl",
            "scope": "#\/properties\/internalAssessmentInternalNote",
            "options": {
                "readonly": true,
                "format": "textarea"
            }
        }
    ]
}');

insert into public.subsidy_stage_uis ("id", "subsidy_stage_id", "version", "status", "input_ui", "view_ui")
values ('1bfbe885-da71-4783-92c1-dfbe8cd6b379', 'f343892a-17a8-48e5-81b0-6c3cb710c29a', 1, 'published', '{
    "type": "FormGroupControl",
    "options": {
        "section": true,
        "group": true
    },
    "elements": [
        {
            "type": "CustomControl",
            "scope": "#\/properties\/implementationCoordinatorAssessment",
            "options": {
                "format": "radio"
            }
        },
        {
            "type": "CustomControl",
            "scope": "#\/properties\/amount",
            "options": []
        },
        {
            "type": "Group",
            "label": "Toelichting",
            "elements": [
                {
                    "type": "VerticalLayout",
                    "elements": [
                        {
                            "type": "CustomControl",
                            "scope": "#\/properties\/implementationCoordinatorAssessmentInternalNote",
                            "options": {
                                "format": "textarea"
                            }
                        }
                    ]
                }
            ]
        }
    ]
}', '{
    "type": "FormGroupControl",
    "options": {
        "section": true
    },
    "elements": [
        {
            "type": "CustomControl",
            "scope": "#\/properties\/amount",
            "options": {
                "readonly": true
            }
        },
        {
            "type": "CustomControl",
            "scope": "#\/properties\/implementationCoordinatorAssessment",
            "options": {
                "readonly": true,
                "format": "radio"
            }
        },
        {
            "type": "CustomControl",
            "scope": "#\/properties\/implementationCoordinatorAssessmentInternalNote",
            "options": {
                "readonly": true,
                "format": "textarea"
            }
        }
    ]
}');

insert into public.subsidy_stage_transitions ("id", "description", "current_subsidy_stage_id", "target_subsidy_stage_id",
                                         "target_application_status", "condition", "send_message",
                                         "assign_to_previous_assessor", "clone_data")
values ('3c6f4891-3b98-4f15-8e3a-fc81f702d3ae', 'Aanvraag ingediend', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8',
        'fb21ee98-9f58-40b1-9432-fad2937688dc', 'pending', null, false, true, true);

insert into public.subsidy_stage_transitions ("id", "description", "current_subsidy_stage_id", "target_subsidy_stage_id",
                                         "target_application_status", "condition", "send_message",
                                         "assign_to_previous_assessor", "evaluation_trigger", "clone_data")
values ('7c8c1355-493a-445e-98e6-aa30d234892e', 'Geen aanvulling ingediend binnen gestelde termijn',
        '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'fb21ee98-9f58-40b1-9432-fad2937688dc', 'pending', null, false, true,
        'expiration', true);

insert into public.subsidy_stage_transitions ("id", "description", "current_subsidy_stage_id", "target_subsidy_stage_id",
                                         "target_application_status", "condition", "send_message", "clone_data",
                                         "expiration_period")
values ('1047e69c-9107-47bc-bfe4-78464e6fb8d3', 'Aanvulling gevraagd', 'fb21ee98-9f58-40b1-9432-fad2937688dc',
        '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', 'requestForChanges', '{
        "type": "comparison",
        "stage": 2,
        "fieldCode": "firstAssessment",
        "operator": "===",
        "value": "Aanvulling nodig"
    }', true, true, 14);

insert into public.subsidy_stage_transitions ("id", "description", "current_subsidy_stage_id", "target_subsidy_stage_id",
                                         "condition", "send_message")
values ('8c66d02a-9ef7-41ff-a1b6-6c747dcadd0c', 'Eerste beoordeling voltooid', 'fb21ee98-9f58-40b1-9432-fad2937688dc',
        'f343892a-17a8-48e5-81b0-6c3cb710c29a', '{
        "type": "in",
        "stage": 2,
        "fieldCode": "firstAssessment",
        "values": [
            "Goedgekeurd",
            "Afgekeurd"
        ]
    }', false);

insert into public.subsidy_stage_transitions ("id", "description", "current_subsidy_stage_id", "target_subsidy_stage_id",
                                         "condition", "send_message", "assign_to_previous_assessor", "clone_data")
values ('da58c18a-0645-4404-bbe4-a186babc01e2', 'Uitvoeringscoordinator oneens met eerste beoordeling',
        'f343892a-17a8-48e5-81b0-6c3cb710c29a', 'fb21ee98-9f58-40b1-9432-fad2937688dc', '{
        "type": "comparison",
        "stage": 3,
        "fieldCode": "implementationCoordinatorAssessment",
        "operator": "===",
        "value": "Oneens met de eerste beoordeling"
    }', false, true, true);

insert into public.subsidy_stage_transitions ("id", "description", "current_subsidy_stage_id", "target_subsidy_stage_id",
                                         "condition", "send_message")
values ('1c375d68-d9bb-4343-b14e-692ce893b64c', 'Interne beoordeling eens met eerste beoordeling',
        'f343892a-17a8-48e5-81b0-6c3cb710c29a', 'f36ae9b6-1340-453f-8ca7-611bfe9b94cd', '{
        "type": "comparison",
        "stage": 3,
        "fieldCode": "implementationCoordinatorAssessment",
        "operator": "===",
        "value": "Eens met de eerste beoordeling"
    }', false);

insert into public.subsidy_stage_transitions ("id", "description", "current_subsidy_stage_id", "target_subsidy_stage_id",
                                         "condition", "send_message", "assign_to_previous_assessor", "clone_data")
values ('383f4e2b-8d5d-4a64-a3d6-001caa857a31', 'Interne controleur oneens met eerste beoordeling',
        'f36ae9b6-1340-453f-8ca7-611bfe9b94cd', 'fb21ee98-9f58-40b1-9432-fad2937688dc', '{
        "type": "comparison",
        "stage": 4,
        "fieldCode": "internalAssessment",
        "operator": "===",
        "value": "Oneens met de eerste beoordeling"
    }', false, true, true);

insert into public.subsidy_stage_transitions ("id", "description", "current_subsidy_stage_id", "target_subsidy_stage_id",
                                         "target_application_status", "condition", "send_message",
                                         "assign_to_previous_assessor", "clone_data")
values ('5e938249-b011-4b82-a700-1a4a55170492', 'Interne controleur eens met afkeuring',
        'f36ae9b6-1340-453f-8ca7-611bfe9b94cd', null, 'rejected', '{
        "type": "and",
        "conditions": [
            {
                "type": "comparison",
                "stage": 2,
                "fieldCode": "firstAssessment",
                "operator": "===",
                "value": "Afgekeurd"
            },
            {
                "type": "comparison",
                "stage": 4,
                "fieldCode": "internalAssessment",
                "operator": "===",
                "value": "Eens met de eerste beoordeling"
            }
        ]
    }', true, false, false);

insert into public.subsidy_stage_transitions ("id", "description", "current_subsidy_stage_id", "target_subsidy_stage_id",
                                         "target_application_status", "condition", "send_message")
values ('7c2c08be-5216-4abb-b8ba-fe08ac922f90', 'Interne auditor eens met goedkeuring',
        'f36ae9b6-1340-453f-8ca7-611bfe9b94cd', null, 'approved', '{
        "type": "and",
        "conditions": [
            {
                "type": "comparison",
                "stage": 2,
                "fieldCode": "firstAssessment",
                "operator": "===",
                "value": "Goedgekeurd"
            },
            {
                "type": "comparison",
                "stage": 4,
                "fieldCode": "internalAssessment",
                "operator": "===",
                "value": "Eens met de eerste beoordeling"
            }
        ]
    }', true);

insert into public.subsidy_stage_transition_messages ("id", "subsidy_stage_transition_id", "version", "status", "created_at",
                                                 "subject", "content_pdf", "content_html")
values ('a9ed4e8e-932e-43cf-afb6-364ef54403e6', '1047e69c-9107-47bc-bfe4-78464e6fb8d3', 1, 'published',
        '2024-03-11 14:17:20', 'Aanvulling nodig', '{layout ''letter_layout.latte''}

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
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de minister van Onderwijs, Cultuur en Wetenschap,<br/>
        namens deze,<br/>
        afdelingshoofd Dienst Uitvoering Subsidies aan Instellingen,<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        C.H. van der Schaaf
    </p>
{/block}

{block objectionFooter}{/block}

{block sidebar}
    {include parent}
{/block}
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
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de minister van Onderwijs, Cultuur en Wetenschap,<br/>
        namens deze,<br/>
        afdelingshoofd Dienst Uitvoering Subsidies aan Instellingen,<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        C.H. van der Schaaf
    </p>
{/block}
');

insert into public.subsidy_stage_transition_messages ("id", "subsidy_stage_transition_id", "version", "status", "created_at",
                                                 "subject", "content_pdf", "content_html")
values ('350d6eae-0f5e-49aa-9c80-280bcc6efafb', '5e938249-b011-4b82-a700-1a4a55170492', 1, 'published',
        '2024-03-11 14:17:20', 'Aanvraag afgekeurd', '{layout ''letter_layout.latte''}

{block concern}
    Betreft: Afwijzing aanvraag {$content->subsidyTitle}
{/block}

{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.
    </p>

    <h2>Besluit</h2>
    <p>Uw aanvraag moet ik helaas afwijzen. De reden(en) licht ik hieronder toe.</p>
    <p>&nbsp;</p>

    <h2>Motivatie</h2>

    {if $content->stage1->eductionType === ''Primair onderwijs''}
    <p>Uw aanvraag is beoordeeld aan de hand van de criteria uit artikel 6 en 7 van de Regeling. </p>
    {/if}

    {if $content->stage1->eductionType === ''Voorgezet onderwijs''}
    <p>Uw aanvraag is beoordeeld aan de hand van de criteria uit artikel 3 en 6 van de Regeling.</p>
    {/if}

    {if $content->stage2->firstAssessmentRejectedNote}
        <p>Uw aanvraag voldoet niet aan de volgende voorwaarde(n):</p>
        <p>{$content->stage2->firstAssessmentRejectedNote|breakLines}</p>
    {/if}
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de minister van Onderwijs, Cultuur en Wetenschap,<br/>
        namens deze,<br/>
        afdelingshoofd Dienst Uitvoering Subsidies aan Instellingen,<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        C.H. van der Schaaf
    </p>
{/block}


{block objectionFooter}
<footer>
    <h2>Bezwaar</h2>
    <p>Als u belang hebt bij dit besluit, dan kunt u hiertegen binnen 6 weken, gerekend vanaf de verzenddatum, bezwaar
        maken. Stuur uw bezwaarschrift naar DUO, Postbus 30205, 2500 GE Den Haag. U kunt uw bezwaar ook digitaal
        indienen via <a href="https://www.duo.nl/zakelijk/oneens-met-duo/bezwaar-maken.jsp" target="_blank">www.duo.nl/zakelijk/oneens-met-duo/bezwaar-maken.jsp</a>.
    </p>
</footer>
{/block}

{block sidebar}
    {include parent}
{/block}
', '{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.
    </p>

    <h2>Besluit</h2>
    <p>Uw aanvraag moet ik helaas afwijzen. De reden(en) licht ik hieronder toe.</p>

    <h2>Motivatie</h2>

    {if $content->stage1->eductionType === ''Primair onderwijs''}
    <p>Uw aanvraag is beoordeeld aan de hand van de criteria uit artikel 6 en 7 van de Regeling. </p>
    {/if}

    {if $content->stage1->eductionType === ''Voorgezet onderwijs''}
    <p>Uw aanvraag is beoordeeld aan de hand van de criteria uit artikel 3 en 6 van de Regeling.</p>
    {/if}

    {if $content->stage2->firstAssessmentRejectedNote}
    <p>Uw aanvraag voldoet niet aan de volgende voorwaarde(n):<<br/>>
    {$content->stage2->firstAssessmentRejectedNote|breakLines}</p>
    {/if}
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de minister van Onderwijs, Cultuur en Wetenschap,<br/>
        namens deze,<br/>
        afdelingshoofd Dienst Uitvoering Subsidies aan Instellingen,<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        C.H. van der Schaaf
    </p>
{/block}

{block objectionFooter}
<footer>
    <h2>Bezwaar</h2>
    <p>Als u belang hebt bij dit besluit, dan kunt u hiertegen binnen 6 weken, gerekend vanaf de verzenddatum, bezwaar
        maken. Stuur uw bezwaarschrift naar DUO, Postbus 30205, 2500 GE Den Haag. U kunt uw bezwaar ook digitaal
        indienen via <a href="https://www.duo.nl/zakelijk/oneens-met-duo/bezwaar-maken.jsp" target="_blank">www.duo.nl/zakelijk/oneens-met-duo/bezwaar-maken.jsp</a>.
    </p>
</footer>
{/block}

');

insert into public.subsidy_stage_transition_messages ("id", "subsidy_stage_transition_id", "version", "status", "created_at",
                                                 "subject", "content_pdf", "content_html")
values ('9445db1e-2aeb-4434-be02-e57622c28e77', '7c2c08be-5216-4abb-b8ba-fe08ac922f90', 1, 'published',
        '2024-03-11 14:17:20', 'Aanvraag goedgekeurd', '{layout ''letter_layout.latte''}

{block concern}
Betreft: Verlening aanvraag {$content->subsidyTitle}
{/block}

{block content}
<p>Beste lezer,</p>
<p>
    Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.
</p>

<h2>Besluit</h2>
<p>Hierbij ken ik uw aanvraag (gedeeltelijk) toe en stel ik de subsidie (aangepast) vast op {$content->stage3->amount}.</p>

{if $content->stage1->eductionType === ''Primair onderwijs''}
<p>De subsidie is toegekend op grond van artikel 3 van de Regeling en beoordeeld aan de hand van de criteria uit artikel
    6 en 7 van de Regeling.</p>
{/if}

{if $content->stage1->eductionType === ''Voorgezet onderwijs''}
<p>De subsidie is toegekend op grond van artikel 2 van de Regeling en beoordeeld aan de hand van de criteria uit artikel
    3 en 6 van de Regeling.</p>
{/if}

{if $content->stage2->firstAssessmentApprovedNote}
<h2>Motivering bij het besluit</h2>
<p>{$content->stage2->firstAssessmentApprovedNote|breakLines}</p>
{/if}

<h2>Waaraan moet u voldoen?</h2>
<p>U moet voldoen aan de verplichtingen in de wet- en regelgeving in deze beschikking.</p>

<u>Wet- en regelgeving</u>
<p>Op deze subsidie is de volgende wet- en regelgeving van toepassing:
<ul>
    <li>Wet overige OCW subsidies;</li>
    <li>Kaderregeling subsidies OCW, SZW en VWS;</li>
    <li>Subsidieregeling reiskosten DAMU-leerlingen, nr.24900503;</li>
    <li>Algemene wet bestuursrecht;</li>
    <li>Wet bestuurlijke boete bijzondere meldingsplichten die gelden voor subsidies die door ministers zijn
        verleend.
    </li>
</ul>
</p>
<p>De regelgeving kunt u raadplegen via <a href="https://wetten.overheid.nl" target="_blank">wetten.overheid.nl</a>.</p>

<u>Meldingsplicht</u>
<p>U bent verplicht een melding te maken bij de Dienst Uitvoering Subsidies aan Instellingen (DUS-I) wanneer niet, niet
    op tijd of niet geheel zal worden voldaan aan de verplichtingen die aan de subsidie zijn verbonden. U doet in ieder
    geval melding als de leerling dit subsidiejaar met de opleiding stopt.</p>

<u>Verantwoording</u>
<p>De subsidie is direct vastgesteld. Dit betekent dat er na afloop van het subsidiejaar geen verantwoordeling van de
    subsidie nodig is.</p>

<u>Wat als u zich niet aan de voorschriften houdt?</u>
<p>Het niet voldoen aan de verplichtingen kan tot gevolg hebben dat u de subsidie helemaal of gedeeltelijk moet
    terugbetalen.</p>

<h2>Wanneer ontvangt u de subsidie?</h2>
<p>Ik streef ernaar het toegekende subsidiebedrag binnen 10 werkdagen naar u over te maken onder vermelding van het
    referentienummer Dossiernummer.</p>

{/block}

{block signature}
<p>
    Met vriendelijke groet,<br/>
    <br/>
    de minister van Onderwijs, Cultuur en Wetenschap,<br/>
    namens deze,<br/>
    afdelingshoofd Dienst Uitvoering Subsidies aan Instellingen,<br/>
    <br/>
    <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
    <br/>
    C.H. van der Schaaf
</p>
{/block}

{block objectionFooter}
<footer>
    <h2>Bezwaar</h2>
    <p>Als u belang hebt bij dit besluit, dan kunt u hiertegen binnen 6 weken, gerekend vanaf de verzenddatum, bezwaar
        maken. Stuur uw bezwaarschrift naar DUO, Postbus 30205, 2500 GE Den Haag. U kunt uw bezwaar ook digitaal
        indienen via <a href="https://www.duo.nl/zakelijk/oneens-met-duo/bezwaar-maken.jsp" target="_blank">www.duo.nl/zakelijk/oneens-met-duo/bezwaar-maken.jsp</a>.
    </p>
</footer>
{/block}

{block sidebar}
    {include parent}
{/block}
', '{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.
    </p>

    <h2>Besluit</h2>
    <p>Hierbij ken ik uw aanvraag (gedeeltelijk) toe en stel ik de subsidie (aangepast) vast op {$content->stage3->amount}.</p>

    {if $content->stage1->eductionType === ''Primair onderwijs''}
    <p>De subsidie is toegekend op grond van artikel 3 van de Regeling en beoordeeld aan de hand van de criteria uit artikel
        6 en 7 van de Regeling.</p>
    {/if}

    {if $content->stage1->eductionType === ''Voorgezet onderwijs''}
    <p>De subsidie is toegekend op grond van artikel 2 van de Regeling en beoordeeld aan de hand van de criteria uit artikel
        3 en 6 van de Regeling.</p>
    {/if}

    {if $content->stage2->firstAssessmentApprovedNote}
    <h2>Motivering bij het besluit</h2>
    <p>{$content->stage2->firstAssessmentApprovedNote|breakLines}</p>
    {/if}

    <h2>Waaraan moet u voldoen?</h2>
    <p>U moet voldoen aan de verplichtingen in de wet- en regelgeving in deze beschikking.</p>

    <u>Wet- en regelgeving</u>
    <p>Op deze subsidie is de volgende wet- en regelgeving van toepassing:
    <ul>
        <li>Wet overige OCW subsidies;</li>
        <li>Kaderregeling subsidies OCW, SZW en VWS;</li>
        <li>Subsidieregeling reiskosten DAMU-leerlingen, nr.24900503;</li>
        <li>Algemene wet bestuursrecht;</li>
        <li>Wet bestuurlijke boete bijzondere meldingsplichten die gelden voor subsidies die door ministers zijn
            verleend.
        </li>
    </ul>
    </p>
    <p>De regelgeving kunt u raadplegen via <a href="https://wetten.overheid.nl" target="_blank">wetten.overheid.nl</a>.</p>

    <u>Meldingsplicht</u>
    <p>U bent verplicht een melding te maken bij de Dienst Uitvoering Subsidies aan Instellingen (DUS-I) wanneer niet, niet
        op tijd of niet geheel zal worden voldaan aan de verplichtingen die aan de subsidie zijn verbonden. U doet in ieder
        geval melding als de leerling dit subsidiejaar met de opleiding stopt.</p>

    <u>Verantwoording</u>
    <p>De subsidie is direct vastgesteld. Dit betekent dat er na afloop van het subsidiejaar geen verantwoordeling van de
        subsidie nodig is.</p>

    <u>Wat als u zich niet aan de voorschriften houdt?</u>
    <p>Het niet voldoen aan de verplichtingen kan tot gevolg hebben dat u de subsidie helemaal of gedeeltelijk moet
        terugbetalen.</p>

    <h2>Wanneer ontvangt u de subsidie?</h2>
    <p>Ik streef ernaar het toegekende subsidiebedrag binnen 10 werkdagen naar u over te maken onder vermelding van het
        referentienummer Dossiernummer.</p>
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de minister van Onderwijs, Cultuur en Wetenschap,<br/>
        namens deze,<br/>
        afdelingshoofd Dienst Uitvoering Subsidies aan Instellingen,<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        C.H. van der Schaaf
    </p>
{/block}

{block objectionFooter}
    <footer>
        <h2>Bezwaar</h2>
        <p>Als u belang hebt bij dit besluit, dan kunt u hiertegen binnen 6 weken, gerekend vanaf de verzenddatum, bezwaar
            maken. Stuur uw bezwaarschrift naar DUO, Postbus 30205, 2500 GE Den Haag. U kunt uw bezwaar ook digitaal
            indienen via <a href="https://www.duo.nl/zakelijk/oneens-met-duo/bezwaar-maken.jsp" target="_blank">www.duo.nl/zakelijk/oneens-met-duo/bezwaar-maken.jsp</a>.
        </p>
    </footer>
{/block}
');

