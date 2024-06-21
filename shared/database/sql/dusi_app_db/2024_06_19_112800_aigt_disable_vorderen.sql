UPDATE public.fields
SET params = '{
    "default": null,
    "options": [
        "Vaststellen",
        "Uitstellen"
    ]
}'
WHERE code = 'assessment'
  AND subsidy_stage_id = '59ddbc42-8ffc-4e2c-a751-d937714b6df6';

UPDATE public.fields
SET params = '{
    "default": null,
    "options": [
        "Vaststellen",
        "Uitstellen"
    ]
}'
WHERE code = 'assessment'
  AND subsidy_stage_id = '2b06aee1-ea36-41a4-b7ae-74fa53c64a64';

UPDATE public.subsidy_stage_uis
SET
    input_ui         = '{
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
                            }
                        ]
                    },
                    {
                        "type": "VerticalLayout",
                        "elements": [
                            {
                                "type": "CustomControl",
                                "scope": "#\/properties\/subsidyAwardedBefore",
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
                "label": "Financi\u00eble afhandeling",
                "elements": [
                    {
                        "type": "VerticalLayout",
                        "elements": [
                            {
                                "type": "CustomControl",
                                "scope": "#\/properties\/businessPartnerNumber",
                                "label": "Zakenpartnernummer",
                                "options": {
                                    "placeholder": ""
                                }
                            },
                            {
                                "type": "CustomControl",
                                "scope": "#\/properties\/liabilitiesNumber",
                                "label": "Verplichtingennummer",
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
                "label": "Uitkering",
                "elements": [
                    {
                        "type": "VerticalLayout",
                        "elements": [
                            {
                                "type": "CustomControl",
                                "scope": "#\/properties\/amount",
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
    }',
    updated_at       = 'now()',
    view_ui          = '{
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
                        "scope": "#\/properties\/subsidyAwardedBefore",
                        "options": {
                            "readonly": true,
                            "format": "radio"
                        }
                    }
                ]
            },
            {
                "type": "FormGroupControl",
                "label": "Uitkering",
                "options": {
                    "section": true,
                    "headingLevel": "2"
                },
                "elements": [
                    {
                        "type": "CustomControl",
                        "scope": "#\/properties\/amount",
                        "options": {
                            "readonly": true
                        }
                    }
                ]
            },
            {
                "type": "FormGroupControl",
                "label": "Financi\u00eble afhandeling",
                "options": {
                    "section": true,
                    "headingLevel": "2"
                },
                "elements": [
                    {
                        "type": "CustomControl",
                        "scope": "#\/properties\/businessPartnerNumber",
                        "label": "Zakenpartnernummer",
                        "options": {
                            "readonly": true
                        }
                    },
                    {
                        "type": "CustomControl",
                        "scope": "#\/properties\/liabilitiesNumber",
                        "label": "Verplichtingennummer",
                        "options": {
                            "readonly": true
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
    }'
WHERE id = '4aa24ca1-0fa8-45d3-a632-15fd788fbc6e';
