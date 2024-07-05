UPDATE public.fields
SET title            = 'Motivering',
    code             = 'motivation',
    subsidy_stage_id = 'f5f76ce6-e2ed-4abf-a38e-103b8dfd2b82'
WHERE code = 'reclaimMotivation'
  AND subsidy_stage_id = '0c2c1f22-624c-45fc-bb20-a3249b647fa7';

UPDATE public.fields
SET subsidy_stage_id = 'f5f76ce6-e2ed-4abf-a38e-103b8dfd2b82'
WHERE code = 'newAssignationAmount'
  AND subsidy_stage_id = '0c2c1f22-624c-45fc-bb20-a3249b647fa7';

UPDATE public.fields
SET subsidy_stage_id = 'f5f76ce6-e2ed-4abf-a38e-103b8dfd2b82'
WHERE code = 'reclaimAmount'
  AND subsidy_stage_id = '0c2c1f22-624c-45fc-bb20-a3249b647fa7';

INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition, retention_period_on_approval, exclude_from_clone_data)
VALUES ('21ffcaa6-7129-4c74-bac5-04b3235d9b28', 'Motivering', null, 'text', 'null', false, 'motivation', 'user',
        '1ec333d3-4b9c-437f-a04d-c1f6a7b70446', null, 'short', false);

INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition, retention_period_on_approval, exclude_from_clone_data)
VALUES ('3d5ce64a-d044-45c3-9082-25d9e9bb6550', 'Vastgesteld bedrag', null, 'text:float', 'null', false,
        'newAssignationAmount', 'user', '1ec333d3-4b9c-437f-a04d-c1f6a7b70446', null, 'short', false);

INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition, retention_period_on_approval, exclude_from_clone_data)
VALUES ('eac3e55e-b657-4547-9150-d1e291a09c15', 'Te vorderen bedrag', null, 'text:float', 'null', false,
        'reclaimAmount', 'user', '1ec333d3-4b9c-437f-a04d-c1f6a7b70446', null, 'short', false);

UPDATE public.subsidy_stage_uis
SET input_ui   = '{
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
                            "scope": "#\/properties\/assessmentDelayChecklist",
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
            "label": "Toelichting",
            "elements": [
                {
                    "type": "VerticalLayout",
                    "elements": [
                        {
                            "type": "CustomControl",
                            "scope": "#\/properties\/proof",
                            "options": {
                                "accept": "image\/jpeg,image\/png,application\/pdf",
                                "maxFileSize": 20971520,
                                "minItems": 1,
                                "maxItems": 20,
                                "tip": "Upload een of meerdere bewijsstukken. Toegestane bestandstypen: pdf, jpg, jpeg, png. Maximale bestandsgrootte: 20 MB."
                            }
                        },
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
        },
        {
            "type": "Group",
            "label": "Vorderen",
            "elements": [
                {
                    "type": "CustomControl",
                    "scope": "#\/properties\/motivation",
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
            ],
            "rule": {
                "effect": "SHOW",
                "condition": {
                    "scope": "#\/properties\/assessment",
                    "schema": {
                        "const": "Vorderen"
                    }
                }
            }
        }
    ]
}',
    updated_at = 'now()',
    view_ui    = '{
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
                        "scope": "#\/properties\/assessmentDelayChecklist",
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
                "type": "FormResultsTable",
                "options": {
                    "fields": {
                        "Bewijs": "{proof}",
                        "Interne notitie": "{internalNote}"
                    },
                    "showEmptyFields": true
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
                                "Motivatie": "{motivation}",
                                "Vastgesteld bedrag": "{newAssignationAmount}",
                                "Te vorderen bedrag": "{reclaimAmount}"
                            }
                        },
                        "rule": {
                            "effect": "SHOW",
                            "condition": {
                                "scope": "#\/properties\/assessment",
                                "schema": {
                                    "const": "Vorderen"
                                }
                            }
                        }
                    }
                ]
            }
        ]
    }'
WHERE id = 'ef196de1-5c15-4af3-9ec8-046ca4419fd1';

UPDATE public.subsidy_stage_uis
SET input_ui   = '{
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
                            "scope": "#\/properties\/assessmentDelayChecklist",
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
            "label": "Toelichting",
            "elements": [
                {
                    "type": "VerticalLayout",
                    "elements": [
                        {
                            "type": "CustomControl",
                            "scope": "#\/properties\/proof",
                            "options": {
                                "accept": "image\/jpeg,image\/png,application\/pdf",
                                "maxFileSize": 20971520,
                                "minItems": 1,
                                "maxItems": 20,
                                "tip": "Upload een of meerdere bewijsstukken. Toegestane bestandstypen: pdf, jpg, jpeg, png. Maximale bestandsgrootte: 20 MB."
                            }
                        },
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
        },
        {
            "type": "Group",
            "label": "Vorderen",
            "elements": [
                {
                    "type": "CustomControl",
                    "scope": "#\/properties\/motivation",
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
            ],
            "rule": {
                "effect": "SHOW",
                "condition": {
                    "scope": "#\/properties\/assessment",
                    "schema": {
                        "const": "Vorderen"
                    }
                }
            }
        }
    ]
}',
    updated_at = 'now()',
    view_ui    = '{
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
                        "scope": "#\/properties\/assessmentDelayChecklist",
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
                "type": "FormResultsTable",
                "options": {
                    "fields": {
                        "Bewijs": "{proof}",
                        "Interne notitie": "{internalNote}"
                    },
                    "showEmptyFields": true
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
                                "Motivatie": "{motivation}",
                                "Vastgesteld bedrag": "{newAssignationAmount}",
                                "Te vorderen bedrag": "{reclaimAmount}"
                            }
                        },
                        "rule": {
                            "effect": "SHOW",
                            "condition": {
                                "scope": "#\/properties\/assessment",
                                "schema": {
                                    "const": "Vorderen"
                                }
                            }
                        }
                    }
                ]
            }
        ]
    }'
WHERE id = 'd15ff747-b912-4abc-b6df-2a750c820d92';

UPDATE public.subsidy_stage_uis
SET input_ui   = '{
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
    updated_at = 'now()',
    view_ui    = '{
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
                                "Vorderingsnummer": "{reclaimNumber}",
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
    <p>Met mijn brief van {$content->stage4->closedAt|date:"d-m-Y"} heb ik u een subsidie verleend van
        {$content->stage2->amount} voor de regeling \'{$content->subsidyTitle}\'. Uit de aan mij ter beschikking
        gestelde gegevens blijkt dat u inmiddels voldoet aan de vereisten voor deze regeling.
    </p>

    <h2>Ambtshalve subsidievaststelling</h2>
    <p>Hierbij deel ik u mede dat ik uw verleende subsidie voor de borstprothesen transvrouwen met
        kenmerk {$content->reference}, ambtshalve vaststel op {$content->stage2->amount}.</p>

    <p>De subsidie is vastgesteld op grond van:<br/>
        <ul>
            <li>Kaderwet VWS-subsidies</li>
            <li>Algemene wet bestuursrecht, in het bijzonder titel 4.2 Subsidies;</li>
            <li>Subsidieregeling borstprothesen transvrouwen</li>
        </ul>
    </p>

    <h2>Hoe wordt de subsidie afgehandeld?</h2>
    <p>
        De subsidie is reeds volledig aan u uitbetaald. Er zal geen verdere betaling of terugvordering plaatsvinden.
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
    Betreft: Ambtshalve vaststelling subsidie \'{$content->subsidyTitle}\'
{/block}

{block content}
    <p>Beste lezer,</p>
    <p>Met mijn brief van {$content->stage4->closedAt|date:"d-m-Y"} heb ik u een subsidie verleend van
        {$content->stage2->amount} voor de regeling \'{$content->subsidyTitle}\'. Uit de aan mij ter beschikking
        gestelde gegevens blijkt dat u inmiddels voldoet aan de vereisten voor deze regeling.
    </p>

    <h2>Ambtshalve subsidievaststelling</h2>
    <p>Hierbij deel ik u mede dat ik uw verleende subsidie voor de borstprothesen transvrouwen met
        kenmerk {$content->reference}, ambtshalve vaststel op {$content->stage2->amount}.</p>

    <p>De subsidie is vastgesteld op grond van:<br/>
        <ul>
            <li>Kaderwet VWS-subsidies</li>
            <li>Algemene wet bestuursrecht, in het bijzonder titel 4.2 Subsidies;</li>
            <li>Subsidieregeling borstprothesen transvrouwen</li>
        </ul>
    </p>

    <h2>Hoe wordt de subsidie afgehandeld?</h2>
    <p>
        De subsidie is reeds volledig aan u uitbetaald. Er zal geen verdere betaling of terugvordering plaatsvinden.
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

        {if $content->stage5->motivation}
            <p>{$content->stage5->motivation}</p>
        {else}
            {if $content->stage6->motivation}
                <p>{$content->stage6->motivation}</p>
            {/if}
        {/if}

    <h2>Besluit</h2>

    {var $newAssignationAmount = $content->stage5->newAssignationAmount ? $content->stage5->newAssignationAmount : $content->stage6->newAssignationAmount}
    <p>Ik stel de subsidie vast op een lager bedrag dan aan u is verleend, namelijk € {formatCurrency($newAssignationAmount)}.</p>

    <p>Mocht u in de toekomst alsnog gebruiken willen maken van de Subsidieregeling borstprothesen transvrouwen, dan dient u
        een volledig nieuwe aanvraag in te dienen, conform de vereisten die in artikel 7 van de regeling zijn opgenomen. U
        kunt zich niet beroepen op al eerder ingediende documenten.</p>

    <h2>Hoe wordt de subsidie afgehandeld?</h2>

    <p>De subsidie is geheel als voorschot aan u uitbetaald.</p>

    <p>Het verschil tussen het vastgestelde subsidiebedrag en het ontvangen voorschot bedraagt {$content->stage2->amount}. Ik vorder dit
        verschil van u terug op grond van artikel 4:57 van de Algemene wet bestuursrecht.</p>

    {var $reclaimAmount = $content->stage5->reclaimAmount ? $content->stage5->reclaimAmount : $content->stage6->reclaimAmount}
    <p>Ik verzoek u het bedrag van € {formatCurrency($reclaimAmount)} binnen zes weken na de datum van deze beschikking over te
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

    {if $content->stage5->motivation}
        <p>{$content->stage5->motivation}</p>
        {else}
            {if $content->stage6->motivation}
                <p>{$content->stage6->motivation}</p>
            {/if}
    {/if}

    <h2>Besluit</h2>

    {var $newAssignationAmount = $content->stage5->newAssignationAmount ? $content->stage5->newAssignationAmount : $content->stage6->newAssignationAmount}
    <p>Ik stel de subsidie vast op een lager bedrag dan aan u is verleend, namelijk € {formatCurrency($newAssignationAmount)}.</p>

    <p>Mocht u in de toekomst alsnog gebruiken willen maken van de Subsidieregeling borstprothesen transvrouwen, dan dient u
        een volledig nieuwe aanvraag in te dienen, conform de vereisten die in artikel 7 van de regeling zijn opgenomen. U
        kunt zich niet beroepen op al eerder ingediende documenten.</p>

    <h2>Hoe wordt de subsidie afgehandeld?</h2>

    <p>De subsidie is geheel als voorschot aan u uitbetaald.</p>

    <p>Het verschil tussen het vastgestelde subsidiebedrag en het ontvangen voorschot bedraagt {$content->stage2->amount}. Ik vorder dit
        verschil van u terug op grond van artikel 4:57 van de Algemene wet bestuursrecht.</p>

    {var $reclaimAmount = $content->stage5->reclaimAmount ? $content->stage5->reclaimAmount : $content->stage6->reclaimAmount}
    <p>Ik verzoek u het bedrag van € {formatCurrency($reclaimAmount)} binnen zes weken na de datum van deze beschikking over te
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
    <p> {$content->stage7->reclaimNumber}</p>
    {/if}

    <p><i>Correspondentie uitsluitend richten aan het retouradres met vermelding van de datum het kenmerk van deze brief en met
            het onderwerp op de enveloppe.</i></p>
{/block}
',
    updated_at                  = 'now()'
WHERE id = 'd9917011-3baf-4a5f-8b1f-0e8e2b62d0a4';
