INSERT INTO public.fields (id, title, description, type,
                           params, is_required, code, source, subsidy_stage_id,
                           required_condition, retention_period_on_approval, exclude_FROM_clone_data)
VALUES ('b81b666a-207d-4d28-8781-ea4e0d1cc389', 'Motivatie van goedkeuring', null, 'text',
        '{"maxLength": null}', false, 'firstAssessmentApprovedNote', 'user', '8027c102-93ef-4735-ab66-97aa63b836eb',
        null, 'short', false);

DELETE
FROM public.answers
WHERE field_id = (select id
                  FROM public.fields
                  WHERE code = 'coordinatorImplementationApprovalNote' and subsidy_stage_id = '85ed726e-cdbe-444e-8d12-c56f9bed2621');

DELETE FROM public.fields
WHERE code = 'coordinatorImplementationApprovalNote' and subsidy_stage_id = '85ed726e-cdbe-444e-8d12-c56f9bed2621';

UPDATE public.subsidy_stage_uis
SET subsidy_stage_id = '8027c102-93ef-4735-ab66-97aa63b836eb',
    version          = 1,
    status           = 'published',
    input_ui         = '{
        "type": "FormGroupControl",
        "options": {
            "section": true,
            "group": true
        },
        "elements": [
            {
                "type": "Group",
                "label": "Persoonsgegevens",
                "elements": [
                    {
                        "type": "VerticalLayout",
                        "elements": [
                            {
                                "type": "CustomControl",
                                "scope": "#\/properties\/personalDataChecklist",
                                "options": {
                                    "format": "checkbox-group"
                                }
                            }
                        ]
                    }
                ]
            },
            {
                "type": "Group",
                "label": "Vaststellen WIA",
                "elements": [
                    {
                        "type": "VerticalLayout",
                        "elements": [
                            {
                                "type": "CustomControl",
                                "scope": "#\/properties\/wiaChecklist",
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
                                "scope": "#\/properties\/WIADecisionIndicates",
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
                                "scope": "#\/properties\/IVA_Or_WIA_Checklist",
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
                                "scope": "#\/properties\/WIA_RejectedOnHighSalaryChecklist",
                                "options": {
                                    "format": "checkbox-group"
                                }
                            }
                        ]
                    }
                ]
            },
            {
                "type": "Group",
                "label": "Zorgaanbieder en functie",
                "elements": [
                    {
                        "type": "VerticalLayout",
                        "elements": [
                            {
                                "type": "CustomControl",
                                "scope": "#\/properties\/employerChecklist",
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
                                "scope": "#\/properties\/healthcareProviderStatementIsComplete",
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
                                "scope": "#\/properties\/employerName",
                                "options": []
                            }
                        ]
                    },
                    {
                        "type": "VerticalLayout",
                        "elements": [
                            {
                                "type": "CustomControl",
                                "scope": "#\/properties\/healthcareProviderName",
                                "options": []
                            }
                        ]
                    },
                    {
                        "type": "VerticalLayout",
                        "elements": [
                            {
                                "type": "CustomControl",
                                "scope": "#\/properties\/chamberOfCommerceNumberHealthcareProvider",
                                "options": []
                            }
                        ]
                    },
                    {
                        "type": "VerticalLayout",
                        "elements": [
                            {
                                "type": "CustomControl",
                                "scope": "#\/properties\/healthcareProviderChecklist",
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
                                "scope": "#\/properties\/healthcareProviderSBICode",
                                "options": []
                            }
                        ]
                    },
                    {
                        "type": "VerticalLayout",
                        "elements": [
                            {
                                "type": "CustomControl",
                                "scope": "#\/properties\/healthcareProviderAGBCode",
                                "options": []
                            }
                        ]
                    }
                ]
            },
            {
                "type": "Group",
                "label": "Justiti\u00eble inrichting",
                "elements": [
                    {
                        "type": "VerticalLayout",
                        "elements": [
                            {
                                "type": "CustomControl",
                                "scope": "#\/properties\/judicialInstitutionIsEligible",
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
                                "scope": "#\/properties\/applicantFoundInBigRegister",
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
                "label": "Vaststellen post-COVID",
                "elements": [
                    {
                        "type": "VerticalLayout",
                        "elements": [
                            {
                                "type": "CustomControl",
                                "scope": "#\/properties\/postCovidChecklist",
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
                                "scope": "#\/properties\/doctorFoundInBigRegister",
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
                                "scope": "#\/properties\/doctorsCertificateIsComplete",
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
    created_at       = null,
    updated_at       = null,
    view_ui          = '{
        "type": "FormGroupControl",
        "options": {
            "section": true
        },
        "elements": [
            {
                "type": "FormGroupControl",
                "label": "Persoonsgegevens",
                "options": {
                    "section": true,
                    "headingLevel": "2"
                },
                "elements": [
                    {
                        "type": "CustomControl",
                        "scope": "#\/properties\/personalDataChecklist",
                        "options": {
                            "readonly": true,
                            "format": "checkbox-group"
                        }
                    }
                ]
            },
            {
                "type": "FormGroupControl",
                "label": "Vaststellen WIA",
                "options": {
                    "section": true,
                    "headingLevel": "2"
                },
                "elements": [
                    {
                        "type": "CustomControl",
                        "scope": "#\/properties\/wiaChecklist",
                        "options": {
                            "readonly": true,
                            "format": "checkbox-group"
                        }
                    },
                    {
                        "type": "CustomControl",
                        "scope": "#\/properties\/WIADecisionIndicates",
                        "options": {
                            "readonly": true,
                            "format": "radio"
                        }
                    },
                    {
                        "type": "CustomControl",
                        "scope": "#\/properties\/IVA_Or_WIA_Checklist",
                        "options": {
                            "readonly": true,
                            "format": "checkbox-group"
                        }
                    },
                    {
                        "type": "CustomControl",
                        "scope": "#\/properties\/WIA_RejectedOnHighSalaryChecklist",
                        "options": {
                            "readonly": true,
                            "format": "checkbox-group"
                        }
                    }
                ]
            },
            {
                "type": "FormGroupControl",
                "label": "Zorgaanbieder en functie",
                "options": {
                    "section": true,
                    "headingLevel": "2"
                },
                "elements": [
                    {
                        "type": "CustomControl",
                        "scope": "#\/properties\/employerChecklist",
                        "options": {
                            "readonly": true,
                            "format": "checkbox-group"
                        }
                    },
                    {
                        "type": "CustomControl",
                        "scope": "#\/properties\/healthcareProviderStatementIsComplete",
                        "options": {
                            "readonly": true,
                            "format": "radio"
                        }
                    },
                    {
                        "type": "CustomControl",
                        "scope": "#\/properties\/employerName",
                        "options": {
                            "readonly": true
                        }
                    },
                    {
                        "type": "CustomControl",
                        "scope": "#\/properties\/healthcareProviderName",
                        "options": {
                            "readonly": true
                        }
                    },
                    {
                        "type": "CustomControl",
                        "scope": "#\/properties\/chamberOfCommerceNumberHealthcareProvider",
                        "options": {
                            "readonly": true
                        }
                    },
                    {
                        "type": "CustomControl",
                        "scope": "#\/properties\/healthcareProviderChecklist",
                        "options": {
                            "readonly": true,
                            "format": "checkbox-group"
                        }
                    },
                    {
                        "type": "CustomControl",
                        "scope": "#\/properties\/healthcareProviderSBICode",
                        "options": {
                            "readonly": true
                        }
                    },
                    {
                        "type": "CustomControl",
                        "scope": "#\/properties\/healthcareProviderAGBCode",
                        "options": {
                            "readonly": true
                        }
                    }
                ]
            },
            {
                "type": "FormGroupControl",
                "label": "Justiti\u00eble inrichting",
                "options": {
                    "section": true,
                    "headingLevel": "2"
                },
                "elements": [
                    {
                        "type": "CustomControl",
                        "scope": "#\/properties\/judicialInstitutionIsEligible",
                        "options": {
                            "readonly": true,
                            "format": "radio"
                        }
                    },
                    {
                        "type": "CustomControl",
                        "scope": "#\/properties\/applicantFoundInBigRegister",
                        "options": {
                            "readonly": true,
                            "format": "radio"
                        }
                    }
                ]
            },
            {
                "type": "FormGroupControl",
                "label": "Vaststellen post-COVID",
                "options": {
                    "section": true,
                    "headingLevel": "2"
                },
                "elements": [
                    {
                        "type": "CustomControl",
                        "scope": "#\/properties\/postCovidChecklist",
                        "options": {
                            "readonly": true,
                            "format": "checkbox-group"
                        }
                    },
                    {
                        "type": "CustomControl",
                        "scope": "#\/properties\/doctorFoundInBigRegister",
                        "options": {
                            "readonly": true,
                            "format": "radio"
                        }
                    },
                    {
                        "type": "CustomControl",
                        "scope": "#\/properties\/doctorsCertificateIsComplete",
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
                        "scope": "#\/properties\/amount",
                        "options": {
                            "readonly": true
                        }
                    },
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
WHERE id = '71f71916-c0ed-45bc-8186-1b4f5dfb69e8';

UPDATE public.subsidy_stage_uis
SET subsidy_stage_id = '85ed726e-cdbe-444e-8d12-c56f9bed2621',
    version          = 1,
    status           = 'published',
    input_ui         = '{
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
                                "scope": "#\/properties\/implementationCoordinatorAssessment",
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
                                "scope": "#\/properties\/coordinatorImplementationReasonForRejection",
                                "options": {
                                    "format": "textarea",
                                    "tip": "Deze notitie wordt opgenomen binnen de brief aan de aanvrager."
                                },
                                "rule": {
                                    "effect": "SHOW",
                                    "condition": {
                                        "scope": "#\/properties\/implementationCoordinatorAssessment",
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
                                "scope": "#\/properties\/coordinatorImplementationInternalNote",
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
    created_at       = null,
    updated_at       = null,
    view_ui          = '{
        "type": "FormGroupControl",
        "options": {
            "section": true
        },
        "elements": [
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
                "scope": "#\/properties\/coordinatorImplementationInternalNote",
                "options": {
                    "readonly": true,
                    "format": "textarea"
                }
            }
        ]
    }'
WHERE id = 'c51302f6-e131-45ff-8d4b-f4ff4a39b52f';

UPDATE public.subsidy_stage_transition_messages SET subsidy_stage_transition_id = 'a27195df-9825-4d18-acce-9b3492221d8a', version = 1, status = 'published', subject = 'Aanvraag goedgekeurd', content_html = e'{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de \'Regeling {$content->subsidyTitle}\' met referentienummer {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>
    <p>&nbsp;</p>

    <h2>Besluit</h2>
    <p>Namens het kabinet verleen ik u hierbij de financiële ondersteuning van {$content->stage2->amount} volgens de ‘Regeling
        zorgmedewerkers met langdurige post-COVID klachten’. U ontvangt dit als gebaar ter erkenning voor uw ontstane
        leed en uw getoonde inzet in de zorg tijdens de uitzonderlijke situatie van de eerste golf in de
        COVID-pandemie.</p>

    {if $content->stage2->firstAssessmentApprovedNote}
        <h2>Motivering bij het besluit</h2>
        <p>{$content->stage2->firstAssessmentApprovedNote|breakLines}</p>
        <p>&nbsp;</p>
    {/if}

    <h2>Wanneer ontvangt u de financiële ondersteuning?</h2>
    <p>Het bedrag van {$content->stage2->amount} wordt in één keer uitbetaald. Wij streven ernaar de financiële
        ondersteuning binnen 10 werkdagen uit te keren.</p>

    <h2>Verder willen wij u wijzen op de volgende punten:</h2>
    <ul>
        <li>De financiële ondersteuning is eenmalig en telt daarom niet voor de inkomensbelasting (Box 1). Het heeft ook
            geen gevolgen voor uw WIA-uitkering. Het bedrag wordt wel onderdeel van het vermogen en kan hier belast
            worden (Box 3). Voor toeslagen, de bijstand en de eigen bijdragen voor zorg op basis van de Wlz of Wmo wordt
            gekeken naar uw vermogen. Het kabinet werkt aan een tijdelijke uitzondering van deze financiële
            ondersteuning voor de vermogenstoets. Op de website van DUS-I kunt u lezen of dit voor u relevant is en hoe
            u de uitzondering kunt aanvragen. Let op: U moet de uitzondering dus zelf aanvragen.
        </li>
        <li>De financiële ondersteuning wordt direct vastgesteld. Dat betekent dat u geen verantwoording hoeft in te
            dienen.
        </li>
    </ul>
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de Minister voor Langdurige Zorg en Sport,<br/>
        namens deze,<br/>
        het afdelingshoofd van Dienst Uitvoering Subsidies aan Instellingen<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(\'vws_dusi_signature.jpg\')|dataStream}" />
        <br/>
        L. van der Weij
    </p>
{/block}
', content_pdf = e'{layout \'letter_layout.latte\'}

{block concern}
    Betreft: Toekenning aanvraag \'Regeling {$content->subsidyTitle}\'
{/block}

{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de \'Regeling {$content->subsidyTitle}\' met referentienummer {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>
    <p>&nbsp;</p>

    <h2>Besluit</h2>
    <p>Namens het kabinet verleen ik u hierbij de financiële ondersteuning van {$content->stage2->amount} volgens de ‘Regeling
        zorgmedewerkers met langdurige post-COVID klachten’. U ontvangt dit als gebaar ter erkenning voor uw ontstane
        leed en uw getoonde inzet in de zorg tijdens de uitzonderlijke situatie van de eerste golf in de
        COVID-pandemie.</p>

    {if $content->stage2->firstAssessmentApprovedNote}
        <h2>Motivering bij het besluit</h2>
        <p>{$content->stage2->firstAssessmentApprovedNote|breakLines}</p>
        <p>&nbsp;</p>
    {/if}

    <h2>Wanneer ontvangt u de financiële ondersteuning?</h2>
    <p>Het bedrag van {$content->stage2->amount} wordt in één keer uitbetaald. Wij streven ernaar de financiële
        ondersteuning binnen 10 werkdagen uit te keren.</p>

    <h2>Verder willen wij u wijzen op de volgende punten:</h2>
    <ul>
        <li>De financiële ondersteuning is eenmalig en telt daarom niet voor de inkomensbelasting (Box 1). Het heeft ook
            geen gevolgen voor uw WIA-uitkering. Het bedrag wordt wel onderdeel van het vermogen en kan hier belast
            worden (Box 3). Voor toeslagen, de bijstand en de eigen bijdragen voor zorg op basis van de Wlz of Wmo wordt
            gekeken naar uw vermogen. Het kabinet werkt aan een tijdelijke uitzondering van deze financiële
            ondersteuning voor de vermogenstoets. Op de website van DUS-I kunt u lezen of dit voor u relevant is en hoe
            u de uitzondering kunt aanvragen. Let op: U moet de uitzondering dus zelf aanvragen.
        </li>
        <li>De financiële ondersteuning wordt direct vastgesteld. Dat betekent dat u geen verantwoording hoeft in te
            dienen.
        </li>
    </ul>
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de Minister voor Langdurige Zorg en Sport,<br/>
        namens deze,<br/>
        het afdelingshoofd van Dienst Uitvoering Subsidies aan Instellingen<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(\'vws_dusi_signature.jpg\')|dataStream}" />
        <br/>
        L. van der Weij
    </p>
{/block}

{block sidebar}
    {include parent}
{/block}
', created_at = '2024-02-06 09:05:32', updated_at = null
WHERE id = '9c2ad81e-cf52-41a3-966f-fc9757de15c9';
