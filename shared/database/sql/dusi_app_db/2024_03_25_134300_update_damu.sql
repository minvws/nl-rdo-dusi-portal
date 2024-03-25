UPDATE public.subsidies
SET title      = 'Reiskosten DAMU-leerlingen (Dans en Muziek)',
    updated_at = 'now()'
WHERE id = '7b9f1318-4c38-4fe5-881b-074729d95abf';


UPDATE public.fields
SET title                        = 'Jaarinkomen ouder 1',
    description                  = null,
    type                         = 'text:numeric',
    params                       = 'null',
    is_required                  = true,
    code                         = 'annualIncomeParentA',
    source                       = 'user',
    required_condition           = null,
    retention_period_on_approval = 'short',
    exclude_from_clone_data      = false
WHERE subsidy_stage_id = '77996a9c-5c8d-47e1-9a88-e41bf594cfc8'
  AND code = 'annualIncomeParentA';

UPDATE public.fields
SET title                        = 'DAMU-school',
    description                  = null,
    type                         = 'select',
    params                       = '{
        "default": null,
        "options": [
            "Amsterdam - Olympiaschool",
            "Den Haag - School voor Jong Talent",
            "Rotterdam - Nieuwe Park Rozenburgschool"
        ]
    }',
    is_required                  = false,
    code                         = 'damuSchoolPrimary',
    source                       = 'user',
    required_condition           = null,
    retention_period_on_approval = 'short',
    exclude_from_clone_data      = false
WHERE subsidy_stage_id = '77996a9c-5c8d-47e1-9a88-e41bf594cfc8'
  AND code = 'damuSchoolPrimary';

UPDATE public.fields
SET title                        = 'DAMU-school',
    description                  = null,
    type                         = 'select',
    params                       = '{
        "default": null,
        "options": [
            "Amsterdam - Gerrit van der Veen College",
            "Amsterdam - Individueel Voortgezet Kunstzinnig Onderwijs (IVKO)",
            "Arnhem - Beekdal Lyceum",
            "Den Haag - Interfaculteit School voor Jong Talent",
            "Enschede - Het Stedelijk Lyceum, locatie Kottenpark",
            "Haren - Zernike College",
            "Maastricht - Bonnefanten College",
            "Rotterdam - Havo/Vwo voor muziek en dans",
            "Rotterdam - Thorbecke Voortgezet Onderwijs",
            "Tilburg - Koning Willem II College",
            "Venlo - Valuas College"
        ]
    }',
    is_required                  = false,
    code                         = 'damuSchoolSecondary',
    source                       = 'user',
    required_condition           = null,
    retention_period_on_approval = 'short',
    exclude_from_clone_data      = false
WHERE subsidy_stage_id = '77996a9c-5c8d-47e1-9a88-e41bf594cfc8'
  AND code = 'damuSchoolSecondary';

UPDATE public.fields
SET title                        = 'Reisafstand enkele reis (in kilometers)',
    description                  = null,
    type                         = 'text:float',
    params                       = '{
        "maximum": 9999,
        "minimum": 1
    }',
    is_required                  = true,
    code                         = 'travelDistanceSingleTrip',
    source                       = 'user',
    required_condition           = null,
    retention_period_on_approval = 'short',
    exclude_from_clone_data      = false
WHERE subsidy_stage_id = '77996a9c-5c8d-47e1-9a88-e41bf594cfc8'
  AND code = 'travelDistanceSingleTrip';

UPDATE public.fields
SET title                        = 'Inschrijfbewijs DAMU-school',
    description                  = null,
    type                         = 'upload',
    params                       = '{
        "maxItems": 20,
        "minItems": 1,
        "mimeTypes": [
            "image/jpeg",
            "image/png",
            "application/pdf"
        ],
        "maxFileSize": 20971520
    }',
    is_required                  = true,
    code                         = 'proofOfRegistrationDAMUSchool',
    source                       = 'user',
    required_condition           = null,
    retention_period_on_approval = 'short',
    exclude_from_clone_data      = false
WHERE subsidy_stage_id = '77996a9c-5c8d-47e1-9a88-e41bf594cfc8'
  AND code = 'proofOfRegistrationDAMUSchool';

UPDATE public.fields
SET title                        = 'Inschrijfbewijs reguliere school',
    description                  = null,
    type                         = 'upload',
    params                       = '{
        "maxItems": 20,
        "minItems": 1,
        "mimeTypes": [
            "image/jpeg",
            "image/png",
            "application/pdf"
        ],
        "maxFileSize": 20971520
    }',
    is_required                  = true,
    code                         = 'proofOfRegistrationHboCollaborationPartner',
    source                       = 'user',
    required_condition           = null,
    retention_period_on_approval = 'short',
    exclude_from_clone_data      = false
WHERE subsidy_stage_id = '77996a9c-5c8d-47e1-9a88-e41bf594cfc8'
  AND code = 'proofOfRegistrationRegularSchool';

DELETE
FROM public.answers
WHERE field_id = (select id
                  FROM public.fields
                  WHERE code = 'alimonyAmount'
                    and subsidy_stage_id = '77996a9c-5c8d-47e1-9a88-e41bf594cfc8');

DELETE
FROM public.fields
WHERE code = 'alimonyAmount'
  and subsidy_stage_id = '77996a9c-5c8d-47e1-9a88-e41bf594cfc8';

DELETE
FROM public.answers
WHERE field_id = (select id
                  FROM public.fields
                  WHERE code = 'totalDistance'
                    and subsidy_stage_id = '77996a9c-5c8d-47e1-9a88-e41bf594cfc8');

DELETE
FROM public.fields
WHERE code = 'totalDistance'
  and subsidy_stage_id = '77996a9c-5c8d-47e1-9a88-e41bf594cfc8';

DELETE
FROM public.answers
WHERE field_id = (select id
                  FROM public.fields
                  WHERE code = 'ANWBRouteCard'
                    and subsidy_stage_id = '77996a9c-5c8d-47e1-9a88-e41bf594cfc8');

DELETE
FROM public.fields
WHERE code = 'ANWBRouteCard'
  and subsidy_stage_id = '77996a9c-5c8d-47e1-9a88-e41bf594cfc8';

INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition, retention_period_on_approval, exclude_from_clone_data)
VALUES ('8514d213-41e5-4a1b-be9e-8dbc8acd3d5d', 'Adres DAMU-school', null, 'text', 'null', false, 'damuSchoolAddress',
        'user', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', null, 'short', false);

UPDATE public.fields
SET title                        = 'Gecontroleerd',
    description                  = null,
    type                         = 'multiselect',
    params                       = '{
        "options": [
            "Woont de aanvrager niet in Caribisch Nederland?",
            "Is het inschrijvingsbewijs van de DAMU-school aangeleverd?",
            "Is naam van de leerling op het DAMU inschrijvingsbewijs hetzelfde als waarvoor subsidie wordt aangevraagd?",
            "Is het inschrijvingsbewijs van de HBO school aangeleverd?",
            "Is naam van de leerling op het HBO inschrijvingsbewijs hetzelfde als waarvoor subsidie wordt aangevraagd?",
            "Is een recente inkomensverklaring (van beide ouders) aangeleverd (maximaal 2 kalenderjaren oud)?",
            "Zijn onnodige gegevens onleesbaar gemaakt?"
        ]
    }',
    is_required                  = false,
    code                         = 'firstAssessmentChecklist',
    source                       = 'user',
    subsidy_stage_id             = 'fb21ee98-9f58-40b1-9432-fad2937688dc',
    required_condition           = null,
    retention_period_on_approval = 'short',
    exclude_from_clone_data      = false
WHERE subsidy_stage_id = 'fb21ee98-9f58-40b1-9432-fad2937688dc'
  AND code = 'firstAssessmentChecklist';

UPDATE public.fields
SET title                        = 'Is voldaan aan de minimale reisafstand tussen het woonadres en de DAMU-school, volgens de ANWB routeplanner?',
    description                  = null,
    type                         = 'select',
    params                       = '{
        "default": null,
        "options": [
            "Ja",
            "Nee"
        ]
    }',
    is_required                  = false,
    code                         = 'isMinimumTravelDistanceMet',
    source                       = 'user',
    subsidy_stage_id             = 'fb21ee98-9f58-40b1-9432-fad2937688dc',
    required_condition           = null,
    retention_period_on_approval = 'short',
    exclude_from_clone_data      = false
WHERE subsidy_stage_id = 'fb21ee98-9f58-40b1-9432-fad2937688dc'
  AND code = 'isMinimumTravelDistanceMet';

UPDATE public.fields
SET title                        = 'Zakenpartnernummer',
    description                  = null,
    type                         = 'text:numeric',
    params                       = '{
        "minimum": 0
    }',
    is_required                  = false,
    code                         = 'businessPartnerNumber',
    source                       = 'user',
    subsidy_stage_id             = 'fb21ee98-9f58-40b1-9432-fad2937688dc',
    required_condition           = null,
    retention_period_on_approval = 'short',
    exclude_from_clone_data      = false
WHERE subsidy_stage_id = 'fb21ee98-9f58-40b1-9432-fad2937688dc'
  AND code = 'businessPartnerNumber';

UPDATE public.subsidy_stage_transitions
SET current_subsidy_stage_id    = 'f343892a-17a8-48e5-81b0-6c3cb710c29a',
    target_subsidy_stage_id     = 'f36ae9b6-1340-453f-8ca7-611bfe9b94cd',
    target_application_status   = null,
    condition                   = '{
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
    }',
    send_message                = false,
    clone_data                  = false,
    assign_to_previous_assessor = false,
    description                 = 'Interne beoordeling eens met eerste beoordeling',
    expiration_period           = null,
    evaluation_trigger          = 'submit'
WHERE id = '1c375d68-d9bb-4343-b14e-692ce893b64c';

UPDATE public.subsidy_stage_transitions
SET current_subsidy_stage_id    = 'f343892a-17a8-48e5-81b0-6c3cb710c29a',
    target_subsidy_stage_id     = null,
    target_application_status   = 'rejected',
    condition                   = '{
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
                "stage": 3,
                "fieldCode": "implementationCoordinatorAssessment",
                "operator": "===",
                "value": "Eens met de eerste beoordeling"
            }
        ]
    }',
    send_message                = true,
    clone_data                  = false,
    assign_to_previous_assessor = false,
    description                 = 'Implementatie coordinator eens met afkeuring',
    expiration_period           = null,
    evaluation_trigger          = 'submit'
WHERE id = '5e938249-b011-4b82-a700-1a4a55170492';

UPDATE public.subsidy_stage_uis
SET subsidy_stage_id = '77996a9c-5c8d-47e1-9a88-e41bf594cfc8',
    version          = 1,
    status           = 'published',
    input_ui         = '{
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
                                                "label": "Is er sprake van een eenoudergezin?",
                                                "options": {
                                                    "format": "radio",
                                                    "placeholder": "",
                                                    "tip": "Er is alleen sprake van een eenoudergezin als een van de ouders overleden is of als een van de ouders volledig uit beeld is."
                                                }
                                            },
                                            {
                                                "type": "CustomControl",
                                                "scope": "#\/properties\/hasAlimony",
                                                "label": "Ontvangt u kinderalimentatie?",
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
                                                },
                                                "rule": {
                                                    "effect": "SHOW",
                                                    "condition": {
                                                        "scope": "#\/properties\/isSingleParentFamily",
                                                        "schema": {
                                                            "const": "Nee"
                                                        }
                                                    }
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
                                                "label": "DAMU-school",
                                                "options": {
                                                    "format": "select",
                                                    "placeholder": "",
                                                    "remoteAction": [
                                                        "onBlur"
                                                    ]
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
                                                "label": "DAMU-school",
                                                "options": {
                                                    "format": "select",
                                                    "placeholder": "",
                                                    "remoteAction": [
                                                        "onBlur"
                                                    ]
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
                                            },
                                            {
                                                "type": "CustomControl",
                                                "scope": "#\/properties\/damuSchoolAddress",
                                                "label": "DAMU-school adres",
                                                "options": {
                                                    "placeholder": "",
                                                    "readonly": true
                                                },
                                                "rule": {
                                                    "effect": "SHOW",
                                                    "condition": {
                                                        "scope": "#",
                                                        "schema": {
                                                            "required": [
                                                                "educationType"
                                                            ]
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
                                                "label": "Reisafstand enkele reis (in kilometers)",
                                                "options": {
                                                    "placeholder": "",
                                                    "remoteAction": [
                                                        "onBlur"
                                                    ],
                                                    "tip": "Gebruik de <a target=''_blank'' href=''https:\/\/www.anwb.nl\/verkeer\/routeplanner''>ANWB-routeplanner<\/a> met de optie \u2018snelste route\u2019. U mag geen andere routeplanner gebruiken. Kies als route het woonadres naar de DAMU-school. Schakel de optie ''Route op basis van actueel verkeer'' uit en klik op ''Herbereken route''."
                                                }
                                            },
                                            {
                                                "type": "CustomControl",
                                                "scope": "#\/properties\/travelExpenseReimbursement",
                                                "label": "Vergoeding per kilometer",
                                                "options": {
                                                    "readonly": true,
                                                    "placeholder": ""
                                                }
                                            },
                                            {
                                                "type": "CustomControl",
                                                "scope": "#\/properties\/requestedSubsidyAmount",
                                                "label": "Gevraagd subsidiebedrag",
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
                        "residentialPostalCode",
                        "residentialCity",
                        "educationType",
                        "travelDistanceSingleTrip"
                    ],
                    "allOf": [
                        {
                            "if": {
                                "properties": {
                                    "isSingleParentFamily": {
                                        "const": "Nee"
                                    }
                                }
                            },
                            "then": {
                                "required": [
                                    "annualIncomeParentB"
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
                                                    "tip": "In het formulier wordt het bruto jaarinkomen gevraagd van beide ouders of wettelijk vertegenwoordigers. Ook als u niet duurzaam samenleeft. Bij een eenoudergezin geldt dit uiteraard niet.<br\/><\/br\/>De inkomensverklaring (IB60 formulier) kunt u downloaden in Mijn Belastingdienst onder het tabblad Inkomstenbelasting. U kunt de verklaring ook gratis aanvragen bij de Belastingdienst via het telefoonnummer 0800 0543. Toegestane bestandstypen: pdf, jpg, jpeg, png. Maximale bestandsgrootte: 20 MB."
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
                                                "label": "Inschrijfbewijs DAMU-school",
                                                "options": {
                                                    "accept": "image\/jpeg,image\/png,.pdf",
                                                    "maxFileSize": 5242880,
                                                    "minItems": 1,
                                                    "maxItems": 20,
                                                    "tip": "Toegestane bestandstypen: pdf, jpg, jpeg, png. Maximale bestandsgrootte: 20 MB."
                                                }
                                            }
                                        ]
                                    },
                                    {
                                        "type": "VerticalLayout",
                                        "elements": [
                                            {
                                                "type": "CustomControl",
                                                "scope": "#\/properties\/proofOfRegistrationHboCollaborationPartner",
                                                "label": "Inschrijfbewijs HBO samenwerkingspartner",
                                                "options": {
                                                    "accept": "image\/jpeg,image\/png,.pdf",
                                                    "maxFileSize": 5242880,
                                                    "minItems": 1,
                                                    "maxItems": 20,
                                                    "tip": "Toegestane bestandstypen: pdf, jpg, jpeg, png. Maximale bestandsgrootte: 20 MB."
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
                        "proofOfRegistrationDAMUSchool",
                        "proofOfRegistrationHboCollaborationPartner"
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
                                        "label": "Waarheidsverklaring",
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
    }',
    updated_at                  = 'now()',
    view_ui          = '{
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
                                "DAMU-school primair onderwijs": "{damuSchoolPrimary}"
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
                                "DAMU-school primair onderwijs": "{damuSchoolSecondary}"
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
                    },
                    {
                        "type": "FormResultsTable",
                        "options": {
                            "fields": {
                                "DAMU-school adres": "{damuSchoolAddress}"
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
                                "Reisafstand enkele reis (in kilometers)": "{travelDistanceSingleTrip}",
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
                                "Inschrijfbewijs DAMU-school": "{proofOfRegistrationDAMUSchool}",
                                "Inschrijfbewijs reguliere school": "{proofOfRegistrationHboCollaborationPartner}"
                            }
                        }
                    }
                ]
            }
        ]
    }'
WHERE id = 'bc406dd2-c425-4577-bb8e-b72453cae5bd';

UPDATE public.subsidy_stage_transition_messages
SET subsidy_stage_transition_id = '1047e69c-9107-47bc-bfe4-78464e6fb8d3',
    version                     = 1,
    status                      = 'published',
    subject                     = 'Aanvulling nodig',
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
        <img class="signature" alt="handtekening" src="{$content->getSignature(\'vws_dusi_signature.jpg\')|dataStream}" />
        <br/>
        J. Kraaijeveld
    </p>
{/block}
',
    content_pdf                 = e'{layout \'letter_layout.latte\'}

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
        <img class="signature" alt="handtekening" src="{$content->getSignature(\'vws_dusi_signature.jpg\')|dataStream}" />
        <br/>
        J. Kraaijeveld
    </p>
{/block}

{block objectionFooter}{/block}

{block sidebar}
    {include parent}
{/block}
',
    updated_at                  = 'now()'
WHERE id = 'a9ed4e8e-932e-43cf-afb6-364ef54403e6';

UPDATE public.subsidy_stage_transition_messages
SET subsidy_stage_transition_id = '5e938249-b011-4b82-a700-1a4a55170492',
    version                     = 1,
    status                      = 'published',
    subject                     = 'Aanvraag afgekeurd',
    content_html                = e'{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.
    </p>

    <h2>Besluit</h2>
    <p>Uw aanvraag moet ik helaas afwijzen. De reden(en) licht ik hieronder toe.</p>
    <p>&nbsp;</p>

    <h2>Motivatie</h2>

    {if $content->stage1->educationType === \'Primair onderwijs\'}
    <p>Uw aanvraag is beoordeeld aan de hand van de criteria uit artikel 6 en 7 van de Regeling.</p>
    {/if}

    {if $content->stage1->educationType === \'Voorgezet onderwijs\'}
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
        <img class="signature" alt="handtekening" src="{$content->getSignature(\'vws_dusi_signature.jpg\')|dataStream}" />
        <br/>
        J. Kraaijeveld
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

',
    content_pdf                 = e'{layout \'letter_layout.latte\'}

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

    {if $content->stage1->educationType === \'Primair onderwijs\'}
    <p>Uw aanvraag is beoordeeld aan de hand van de criteria uit artikel 6 en 7 van de Regeling.</p>
    {/if}

    {if $content->stage1->educationType === \'Voorgezet onderwijs\'}
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
        <img class="signature" alt="handtekening" src="{$content->getSignature(\'vws_dusi_signature.jpg\')|dataStream}" />
        <br/>
        J. Kraaijeveld
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
',
    updated_at                  = 'now()'
WHERE id = '350d6eae-0f5e-49aa-9c80-280bcc6efafb';

UPDATE public.subsidy_stage_transition_messages
SET subsidy_stage_transition_id = '7c2c08be-5216-4abb-b8ba-fe08ac922f90',
    version                     = 1,
    status                      = 'published',
    subject                     = 'Aanvraag goedgekeurd',
    content_html                = e'{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.
    </p>

    <h2>Besluit</h2>
    <p>Hierbij ken ik uw aanvraag (gedeeltelijk) toe en stel ik de subsidie (aangepast) vast op € {$content->stage3->amount}. U vroeg een subsidie aan van € {$content->stage1->requestedSubsidyAmount}.</p>

    {if $content->stage1->educationType === \'Primair onderwijs\'}
    <p>De subsidie is toegekend op grond van artikel 3 van de Regeling en beoordeeld aan de hand van de criteria uit artikel
        6 en 7 van de Regeling.</p>
    {/if}

    {if $content->stage1->educationType === \'Voorgezet onderwijs\'}
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
    <p>De subsidie is direct vastgesteld. Dit betekent dat er na afloop van het subsidiejaar geen verantwoording van de
        subsidie nodig is.</p>

    <u>Wat als u zich niet aan de voorschriften houdt?</u>
    <p>Het niet voldoen aan de verplichtingen kan tot gevolg hebben dat u de subsidie helemaal of gedeeltelijk moet
        terugbetalen.</p>

    <h2>Wanneer ontvangt u de subsidie?</h2>
    <p>Ik streef ernaar het toegekende subsidiebedrag binnen 10 werkdagen naar u over te maken onder vermelding van het
        referentienummer {$content->reference}.</p>
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de minister van Onderwijs, Cultuur en Wetenschap,<br/>
        namens deze,<br/>
        afdelingshoofd Dienst Uitvoering Subsidies aan Instellingen,<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(\'vws_dusi_signature.jpg\')|dataStream}" />
        <br/>
        J. Kraaijeveld
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
',
    content_pdf                 = e'{layout \'letter_layout.latte\'}

{block concern}
Betreft: Verlening aanvraag {$content->subsidyTitle}
{/block}

{block content}
<p>Beste lezer,</p>
<p>
    Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.
</p>

<h2>Besluit</h2>
<p>Hierbij ken ik uw aanvraag (gedeeltelijk) toe en stel ik de subsidie (aangepast) vast op € {$content->stage3->amount}. U vroeg een subsidie aan van € {$content->stage1->requestedSubsidyAmount}.</p>

{if $content->stage1->educationType === \'Primair onderwijs\'}
<p>De subsidie is toegekend op grond van artikel 3 van de Regeling en beoordeeld aan de hand van de criteria uit artikel
    6 en 7 van de Regeling.</p>
{/if}

{if $content->stage1->educationType === \'Voorgezet onderwijs\'}
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
<p>De subsidie is direct vastgesteld. Dit betekent dat er na afloop van het subsidiejaar geen verantwoording van de
    subsidie nodig is.</p>

<u>Wat als u zich niet aan de voorschriften houdt?</u>
<p>Het niet voldoen aan de verplichtingen kan tot gevolg hebben dat u de subsidie helemaal of gedeeltelijk moet
    terugbetalen.</p>

<h2>Wanneer ontvangt u de subsidie?</h2>
<p>Ik streef ernaar het toegekende subsidiebedrag binnen 10 werkdagen naar u over te maken onder vermelding van het
    referentienummer {$content->reference}.</p>

{/block}

{block signature}
<p>
    Met vriendelijke groet,<br/>
    <br/>
    de minister van Onderwijs, Cultuur en Wetenschap,<br/>
    namens deze,<br/>
    afdelingshoofd Dienst Uitvoering Subsidies aan Instellingen,<br/>
    <br/>
    <img class="signature" alt="handtekening" src="{$content->getSignature(\'vws_dusi_signature.jpg\')|dataStream}" />
    <br/>
    J. Kraaijeveld
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
',
    updated_at                  = 'now()'
WHERE id = '9445db1e-2aeb-4434-be02-e57622c28e77';
