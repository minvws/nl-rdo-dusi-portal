UPDATE public.subsidy_stage_uis
SET
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
                                            },
                                            {
                                                "type": "CustomControl",
                                                "scope": "#\/properties\/bankStatement",
                                                "label": "Bankafschrift",
                                                "options": {
                                                    "accept": "image\/jpeg,image\/png,application\/pdf",
                                                    "maxFileSize": 20971520,
                                                    "minItems": 1,
                                                    "maxItems": 20,
                                                    "tip": "De ingevoerde naam rekeninghouder en IBAN konden niet worden geverifieerd.\n\nOp de kopie van een recent bankafschrift moeten het rekeningnummer en uw naam zichtbaar zijn. Adres en datum mogen ook, maar zijn niet verplicht. Maak de andere gegevens onleesbaar. U mag ook een afdruk van internet bankieren gebruiken of een kopie van uw bankpas. Zie ook dit <a href=\"https:\/\/www.dus-i.nl\/documenten\/publicaties\/2018\/07\/30\/voorbeeld-bankafschrift\" target=\"_blank\">voorbeeld<\/a>.",
                                                    "validation": [
                                                        "onInput"
                                                    ]
                                                },
                                                "rule": {
                                                    "effect": "SHOW",
                                                    "condition": {
                                                        "scope": "#",
                                                        "schema": {
                                                            "anyOf": [
                                                                {
                                                                    "required": [
                                                                        "validationResult"
                                                                    ],
                                                                    "properties": {
                                                                        "validationResult": {
                                                                            "type": "object",
                                                                            "required": [
                                                                                "bankAccountNumber"
                                                                            ],
                                                                            "properties": {
                                                                                "bankAccountNumber": {
                                                                                    "type": "array",
                                                                                    "items": {
                                                                                        "anyOf": [
                                                                                            {
                                                                                                "type": "object",
                                                                                                "required": [
                                                                                                    "id"
                                                                                                ],
                                                                                                "properties": {
                                                                                                    "id": {
                                                                                                        "const": "validationSurePayError"
                                                                                                    }
                                                                                                }
                                                                                            },
                                                                                            {
                                                                                                "type": "object",
                                                                                                "required": [
                                                                                                    "id"
                                                                                                ],
                                                                                                "properties": {
                                                                                                    "id": {
                                                                                                        "const": "validationSurePayWarning"
                                                                                                    }
                                                                                                }
                                                                                            }
                                                                                        ]
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
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
                                                    "tip": "Er is alleen sprake van een eenoudergezin als een van de ouders overleden is of als een van de ouders volledig uit beeld is.",
                                                    "remoteAction": [
                                                        "onValid"
                                                    ]
                                                }
                                            },
                                            {
                                                "type": "CustomControl",
                                                "scope": "#\/properties\/hasAlimony",
                                                "label": "Ontvangt u kinderalimentatie?",
                                                "options": {
                                                    "format": "radio",
                                                    "placeholder": ""
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
                                                        "onValid"
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
                                                        "onValid"
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
                                                        "onValid"
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
                                "required": [
                                    "validationResult"
                                ],
                                "properties": {
                                    "validationResult": {
                                        "type": "object",
                                        "required": [
                                            "bankAccountNumber"
                                        ],
                                        "properties": {
                                            "bankAccountNumber": {
                                                "type": "array",
                                                "items": {
                                                    "anyOf": [
                                                        {
                                                            "type": "object",
                                                            "required": [
                                                                "id"
                                                            ],
                                                            "properties": {
                                                                "id": {
                                                                    "const": "validationSurePayError"
                                                                }
                                                            }
                                                        }
                                                    ]
                                                }
                                            }
                                        }
                                    }
                                }
                            },
                            "then": {
                                "required": [
                                    "bankStatement"
                                ]
                            }
                        },
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
    }'
WHERE id = 'bc406dd2-c425-4577-bb8e-b72453cae5bd';
