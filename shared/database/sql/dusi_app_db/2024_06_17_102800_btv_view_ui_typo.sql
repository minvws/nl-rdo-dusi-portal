UPDATE public.subsidy_stage_uis
SET
    updated_at       = 'now()',
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
                                "Achternaam": "{lastName}",
                                "Geboortedatum": "{dateOfBirth}"
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
                                "Naam rekeninghouder": "{bankAccountHolder}",
                                "Bankafschrift (indien nodig)": "{bankStatement}"
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
                                "Uittreksel bevolkingsregister": "{extractPopulationRegisterDocument}",
                                "Verklaring behandeltraject": "{proofOfMedicalTreatmentDocument}",
                                "Verklaring type behandeling": "{proofOfTypeOfMedicalTreatmentDocument}"
                            }
                        }
                    }
                ]
            }
        ]
    }'
WHERE id = '72475863-7987-4375-94d7-21e04ff6552b';
