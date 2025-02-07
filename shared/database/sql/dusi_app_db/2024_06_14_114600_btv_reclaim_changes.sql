DELETE
FROM public.answers
WHERE field_id = (SELECT id
                  FROM public.fields
                  WHERE code = 'liabilitiesNumber'
                    AND subsidy_stage_id = '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c');

DELETE
FROM public.fields
WHERE code = 'liabilitiesNumber'
  AND subsidy_stage_id = '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c';

INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition, retention_period_on_approval, exclude_from_clone_data)
VALUES ('bf1fc5ce-98e0-472e-937d-0556d1bf58ee', 'Motivering', null, 'text', 'null', false, 'reclaimMotivation', 'user',
        '0c2c1f22-624c-45fc-bb20-a3249b647fa7', null, 'short', false);

INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition, retention_period_on_approval, exclude_from_clone_data)
VALUES ('4381cb00-c54c-4436-b58f-774333470e46', 'Vastgesteld bedrag', null, 'text:float', 'null', false,
        'newAssignationAmount', 'user', '0c2c1f22-624c-45fc-bb20-a3249b647fa7', null, 'short', false);

INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition, retention_period_on_approval, exclude_from_clone_data)
VALUES ('a2190137-b195-442c-904e-844b2e13aafd', 'Te vorderen bedrag', null, 'text:float', 'null', false,
        'reclaimAmount', 'user', '0c2c1f22-624c-45fc-bb20-a3249b647fa7', null, 'short', false);

UPDATE public.subsidy_stage_uis
SET input_ui = '{
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
    updated_at = 'now()',
    view_ui = '{
        "type": "FormGroupControl",
        "options": {
            "section": true
        },
        "elements": [
            {
                "type": "FormGroupControl",
                "label": "Beoordeling",
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
WHERE id = 'db1076a1-42f3-4c90-b1bf-57d1db025f2e';

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
                                "scope": "#\/properties\/assignationAuditChecklist",
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
                "label": "Status",
                "elements": [
                    {
                        "type": "VerticalLayout",
                        "elements": [
                            {
                                "type": "CustomControl",
                                "scope": "#\/properties\/assessment",
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
                "label": "Vorderen",
                "elements": [
                    {
                        "type": "VerticalLayout",
                        "elements": [
                            {
                                "type": "CustomControl",
                                "scope": "#\/properties\/reclaimNumber"
                            }
                        ]
                    },
                    {
                        "type": "CustomControl",
                        "scope": "#\/properties\/reclaimMotivation",
                        "options": {
                            "format": "textarea"
                        }
                    },
                    {
                        "type": "CustomControl",
                        "scope": "#\/properties\/newAssignationAmount",
                        "options": {
                            "format": "float"
                        }
                    },
                    {
                        "type": "CustomControl",
                        "scope": "#\/properties\/reclaimAmount",
                        "options": {
                            "format": "float"
                        }
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
                                "scope": "#\/properties\/internalNote",
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
                "label": "Beoordeling",
                "options": {
                    "section": true,
                    "headingLevel": "2"
                },
                "elements": [
                    {
                        "type": "CustomControl",
                        "scope": "#\/properties\/assignationAuditChecklist",
                        "options": {
                            "readonly": true,
                            "format": "checkbox-group"
                        }
                    }
                ]
            },
            {
                "type": "CustomControl",
                "scope": "#\/properties\/assessment",
                "options": {
                    "readonly": true,
                    "format": "radio"
                }
            },
            {
                "type": "FormGroupControl",
                "label": "Vorderen",
                "options": {
                    "section": true,
                    "headingLevel": "2"
                },
                "elements": [
                    {
                        "type": "FormResultsTable",
                        "options": {
                            "fields": {
                                "Vorderingsnummer": "{reclaimNumber}",
                                "Motivering": "{reclaimMotivation}",
                                "Vastgesteld bedrag": "{newAssignationAmount}",
                                "Te vorderen bedrag": "{reclaimAmount}"
                            }
                        }
                    }
                ]
            },
            {
                "type": "FormGroupControl",
                "label": "Notities",
                "options": {
                    "section": true,
                    "headingLevel": "2"
                },
                "elements": [
                    {
                        "type": "FormResultsTable",
                        "options": {
                            "fields": {
                                "Interne notitie": "{internalNote}"
                            }
                        }
                    }
                ]
            }
        ]
    }'
WHERE id = 'fe5b6562-9cf9-4c2f-b963-dadc87044766';

UPDATE public.subsidy_stage_transition_messages
SET
    content_html                = e'{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling \'{$content->subsidyTitle}\' met referentienummer {$content->reference}.
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

    {if $content->stage2->firstAssessmentApprovedNote}
        <h2>Motivering bij het besluit</h2>
        <p>{$content->stage2->firstAssessmentApprovedNote|breakLines}</p>
    {/if}

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

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de minister van Volksgezondheid, Welzijn en Sport,<br/>
        namens deze,<br/>
        de directeur Curatieve Zorg,<br/>
        voor deze,<br/>
        het afdelingshoofd Dienst Uitvoering Subsidies aan Instellingen,<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(\'vws_dusi_signature.jpg\')|dataStream}" />
        <br/>
        P.A. van Hecking Colenbrander
    </p>
{/block}
',
    content_pdf                 = e'{layout \'letter_layout.latte\'}

{block concern}
    Betreft: Verlening aanvraag \'{$content->subsidyTitle}\'
{/block}

{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling \'{$content->subsidyTitle}\' met referentienummer {$content->reference}.
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

    {if $content->stage2->firstAssessmentApprovedNote}
        <h2>Motivering bij het besluit</h2>
        <p>{$content->stage2->firstAssessmentApprovedNote|breakLines}</p>
    {/if}

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

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de minister van Volksgezondheid, Welzijn en Sport,<br/>
        namens deze,<br/>
        de directeur Curatieve Zorg,<br/>
        voor deze,<br/>
        het afdelingshoofd Dienst Uitvoering Subsidies aan Instellingen,<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(\'vws_dusi_signature.jpg\')|dataStream}" />
        <br/>
        P.A. van Hecking Colenbrander
    </p>
{/block}

{block sidebar}
    {include parent}

    <h4>Relatienummer</h4>
    <p>{$content->stage2->businessPartnerNumber}</p>

{/block}
',
    updated_at                  = 'now()'
WHERE id = '532d7372-a029-4190-bf8f-c8417ce9acb4';

UPDATE public.subsidy_stage_transition_messages
SET
    content_html                = e'{block content}
    <p>Beste lezer,</p>
    <p>Met mijn brief van {$content->stage4->closedAt|date:"d-m-Y"} heb ik u een subsidie verleend van
        {$content->stage2->amount} voor de regeling \'{$content->subsidyTitle}\'. Uit de aan mij ter beschikking
        gestelde gegevens blijkt dat u inmiddels voldoet aan de vereisten voor deze regeling.
    </p>
    <p>&nbsp;</p>

    <h2>Besluit</h2>
    <p>Ik stel hierbij de subsidie vast op {$content->stage2->amount}.</p>
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

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de minister van Volksgezondheid, Welzijn en Sport,<br/>
        namens deze,<br/>
        de directeur Curatieve Zorg,<br/>
        voor deze,<br/>
        het afdelingshoofd Dienst Uitvoering Subsidies aan Instellingen,<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(\'vws_dusi_signature.jpg\')|dataStream}" />
        <br/>
        P.A. van Hecking Colenbrander
    </p>
{/block}
',
    content_pdf                 = e'{layout \'letter_layout.latte\'}

{block concern}
    Betreft: Vaststelling subsidie \'{$content->subsidyTitle}\'
{/block}

{block content}
    <p>Beste lezer,</p>
    <p>Met mijn brief van {$content->stage4->closedAt|date:"d-m-Y"} heb ik u een subsidie verleend van
        {$content->stage2->amount} voor de regeling \'{$content->subsidyTitle}\'. Uit de aan mij ter beschikking
        gestelde gegevens blijkt dat u inmiddels voldoet aan de vereisten voor deze regeling.
    </p>
    <p>&nbsp;</p>

    <h2>Besluit</h2>
    <p>Ik stel hierbij de subsidie vast op {$content->stage2->amount}.</p>
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

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de minister van Volksgezondheid, Welzijn en Sport,<br/>
        namens deze,<br/>
        de directeur Curatieve Zorg,<br/>
        voor deze,<br/>
        het afdelingshoofd Dienst Uitvoering Subsidies aan Instellingen,<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(\'vws_dusi_signature.jpg\')|dataStream}" />
        <br/>
        P.A. van Hecking Colenbrander
    </p>
{/block}

{block sidebar}
    {include parent}

    <h4>Relatienummer</h4>
    <p>{$content->stage2->businessPartnerNumber}</p>

{/block}
',
    updated_at                  = 'now()'
WHERE id = 'd8c2a8d1-e512-40a1-94f8-6535cc85289c';

UPDATE public.subsidy_stage_transition_messages
SET
    content_html                = e'{block content}
    <p>Beste lezer,</p>

    <p>Bij brief van {$content->lastAllocatedAt|date:\'d-m-Y\'}, met kenmerk {$content->reference}, heb ik aan u een subsidie verleend van
        {$content->stage2->amount} voor de subsidieregeling borstprothesen transvrouwen (hierna: Subsidieregeling). In mijn
        administratie is uw aanvraag bekend onder nummer: {$content->reference}.</p>

    {if $content->stage7->reclaimMotivation}
    <p>{$content->stage7->reclaimMotivation}</p>
    {/if}

    <h2>Besluit</h2>

    <p>Ik stel de subsidie vast op een lager bedrag dan aan u is verleend, namelijk € {formatCurrency($content->stage7->newAssignationAmount)}.</p>

    <p>Mocht u in de toekomst alsnog gebruiken willen maken van de Subsidieregeling borstprothesen transvrouwen, dan dient u
        een volledig nieuwe aanvraag in te dienen, conform de vereisten die in artikel 7 van de regeling zijn opgenomen. U
        kunt zich niet beroepen op al eerder ingediende documenten.</p>

    <h2>Hoe wordt de subsidie afgehandeld?</h2>

    <p>De subsidie is geheel als voorschot aan u uitbetaald.</p>

    <p>Het verschil tussen het vastgestelde subsidiebedrag en het ontvangen voorschot bedraagt {$content->stage2->amount}. Ik vorder dit
        verschil van u terug op grond van artikel 4:57 van de Algemene wet bestuursrecht.</p>

    <p>Ik verzoek u het bedrag van € {formatCurrency($content->stage7->reclaimAmount)} binnen zes weken na de datum van deze beschikking over te
        maken op bankrekeningnummer: NL55INGB0705003566 ten name van VWS — Financieel Dienstencentrum onder vermelding van
        het vorderingsnummer {$content->stage7->reclaimNumber} en subsidienummer {$content->reference}.</p>
{/block}


{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de minister van Volksgezondheid, Welzijn en Sport,<br/>
        namens deze,<br/>
        de directeur Curatieve Zorg,<br/>
        voor deze,<br/>
        het afdelingshoofd Dienst Uitvoering Subsidies aan Instellingen,<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(\'vws_dusi_signature.jpg\')|dataStream}" />
        <br/>
        P.A. van Hecking Colenbrander
    </p>
{/block}
',
    content_pdf                 = e'{layout \'letter_layout.latte\'}

{block concern}
    Betreft: Vordering subsidie \'{$content->subsidyTitle}\'
{/block}

{block content}
    <p>Beste lezer,</p>

    <p>Bij brief van {$content->lastAllocatedAt|date:\'d-m-Y\'}, met kenmerk {$content->reference}, heb ik aan u een subsidie verleend van
        {$content->stage2->amount} voor de subsidieregeling borstprothesen transvrouwen (hierna: Subsidieregeling). In mijn
        administratie is uw aanvraag bekend onder nummer: {$content->reference}.</p>

    {if $content->stage7->reclaimMotivation}
    <p>{$content->stage7->reclaimMotivation}</p>
    {/if}

    <h2>Besluit</h2>

    <p>Ik stel de subsidie vast op een lager bedrag dan aan u is verleend, namelijk € {formatCurrency($content->stage7->newAssignationAmount)}.</p>

    <p>Mocht u in de toekomst alsnog gebruiken willen maken van de Subsidieregeling borstprothesen transvrouwen, dan dient u
    een volledig nieuwe aanvraag in te dienen, conform de vereisten die in artikel 7 van de regeling zijn opgenomen. U
    kunt zich niet beroepen op al eerder ingediende documenten.</p>

    <h2>Hoe wordt de subsidie afgehandeld?</h2>

    <p>De subsidie is geheel als voorschot aan u uitbetaald.</p>

    <p>Het verschil tussen het vastgestelde subsidiebedrag en het ontvangen voorschot bedraagt {$content->stage2->amount}. Ik vorder dit
    verschil van u terug op grond van artikel 4:57 van de Algemene wet bestuursrecht.</p>

    <p>Ik verzoek u het bedrag van € {formatCurrency($content->stage7->reclaimAmount)} binnen zes weken na de datum van deze beschikking over te
    maken op bankrekeningnummer: NL55INGB0705003566 ten name van VWS — Financieel Dienstencentrum onder vermelding van
    het vorderingsnummer {$content->stage7->reclaimNumber} en subsidienummer {$content->reference}.</p>
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de minister van Volksgezondheid, Welzijn en Sport,<br/>
        namens deze,<br/>
        de directeur Curatieve Zorg,<br/>
        voor deze,<br/>
        het afdelingshoofd Dienst Uitvoering Subsidies aan Instellingen,<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(\'vws_dusi_signature.jpg\')|dataStream}" />
        <br/>
        P.A. van Hecking Colenbrander
    </p>
{/block}

{block sidebar}
    {include parent}

    {if ($content->stage2->businessPartnerNumber)}
    <h4>Relatienummer</h4>
    <p>{$content->stage2->businessPartnerNumber}</p>
    {/if}

    {if ($content->stage7->reclaimNumber)}
    <h4>Vorderingsnummer</h4>
    <p>{$content->stage7->reclaimNumber}</p>
    {/if}

    <p><i>Correspondentie uitsluitend richten aan het retouradres met vermelding van de datum het kenmerk van deze brief en met
            het onderwerp op de enveloppe.</i></p>
{/block}
',
    updated_at                  = 'now()'
WHERE id = 'd9917011-3baf-4a5f-8b1f-0e8e2b62d0a4';
