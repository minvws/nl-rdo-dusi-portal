-- DUSI-1736 BTV beoordelingsvragen updaten
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition, retention_period_on_approval, exclude_from_clone_data)
VALUES ('cc3f3ca6-8ad7-4470-b846-af959c978cc0', 'Controlevragen', null, 'multiselect', '{
    "options": [
        "De aanvraag zit in de steekproef",
        "De aanvraag zit niet in de steekproef",
        "De opgevraagde informatie is compleet en akkoord",
        "De vaststelling is geaccordeerd in SAP",
        "Het dossier is compleet (voltoets)"
    ]
}', false, 'assessmentDelayChecklist', 'user', '1ec333d3-4b9c-437f-a04d-c1f6a7b70446', null, 'short', false);

INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition, retention_period_on_approval, exclude_from_clone_data)
VALUES ('8d6274db-280a-4f53-b52e-8dc3b795983e', 'Controlevragen', null, 'multiselect', '{
    "options": [
        "De aanvraag zit in de steekproef",
        "De aanvraag zit niet in de steekproef",
        "De opgevraagde informatie is compleet en akkoord",
        "De vaststelling is geaccordeerd in SAP",
        "Het dossier is compleet (voltoets)"
    ]
}', false, 'assessmentDelayChecklist', 'user', 'f5f76ce6-e2ed-4abf-a38e-103b8dfd2b82', null, 'short', false);

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
                            "label": "Bewijs",
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
                            "label": "Bewijs",
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
            }
        ]
    }'
WHERE id = 'd15ff747-b912-4abc-b6df-2a750c820d92';

INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition, retention_period_on_approval, exclude_from_clone_data)
VALUES ('203c40a4-bdab-42fd-b854-66cc9e3f4615', 'Controlevragen', null, 'multiselect', '{
    "options": [
        "De subsidie mag worden vastgesteld",
        "De vaststelling is geaccordeerd in SAP",
        "Het vorderingsnummer is aangemaakt in SAP (alleen bij terugvorderen)",
        "De beschikking mag verzonden worden"
    ]
}', false, 'assignationAuditChecklist', 'user', '0c2c1f22-624c-45fc-bb20-a3249b647fa7', null, 'short', false);

UPDATE public.fields
SET params = '{
    "options": [
        "De aanvrager valt niet onder de WSNP/bewindvoering",
        "De aanvrager heeft niet eerder een BTV-subsidieaanvraag ingediend",
        "De aanvrager komt niet voor in het M&O register",
        "De persoonsgegevens zijn door de aanvrager juist ingevuld (NAW-gegevens, IBAN)",
        "Uittreksel van het BRP is opgestuurd (< 12 maanden)",
        "De aanvrager is een ingezetene (> 4 maanden) in Nederland",
        "De aanvrager is ouder dan 18 jaar",
        "De ingevoerde persoonsgegevens zijn conform het BRP uittreksel",
        "De medische verklaringen zijn volledig ingevuld en op naam van de aanvrager",
        "De verklaring van de arts over het behandeltraject is minder dan 2 maanden oud",
        "De verklaring van de arts over het behandeltraject is ondertekend en voorzien van een naamstempel",
        "Het opgegeven BIG-nummer komt overeen met het BIG-register",
        "De aanvrager heeft genderdysforie",
        "De aanvrager heeft minimaal een jaar voor de aanvraag hormoonbehandeling ondergaan, of is hiermee vanwege medische redenen gestopt of kon deze om medische redenen niet ondergaan",
        "De verklaring van de arts met de vermelding van het type behandeling is opgestuurd (<12 maanden oud)",
        "De verklaring van de arts met de vermelding van de type behandeling is ondertekend en voorzien van een naamstempel",
        "De type behandeling voldoet aan de voorwaarden conform de subsidieregeling",
        "Het opgegeven IBAN is correct",
        "De verificatiebevestiging met betrekking tot de verklaring over het behandeltraject is ontvangen",
        "De verificatiebevestiging met betrekking tot de verklaring over het type behandeling is ontvangen"
    ]
}'
WHERE code = 'firstAssessmentChecklist'
  AND subsidy_stage_id = '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c';

INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition, retention_period_on_approval, exclude_from_clone_data)
VALUES ('620fd934-2aa1-436f-b38b-b9df89644ed8', 'Vorderingsnummer', null, 'text', 'null', false, 'reclaimNumber',
        'user', '0c2c1f22-624c-45fc-bb20-a3249b647fa7', null, 'short', false);

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
                },
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
                        "scope": "#/properties/assignationAuditChecklist",
                        "options": {
                            "readonly": true,
                            "format": "checkbox-group"
                        }
                    }
                ]
            },
            {
                "type": "CustomControl",
                "scope": "#/properties/assessment",
                "options": {
                    "readonly": true,
                    "format": "radio"
                }
            },
            {
                "type": "FormGroupControl",
                "label": "Vordering",
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
SET content_html = e'{block content}
<p>Beste lezer,</p>

<p>Bij brief van datum verleningsbrief, met kenmerk {$content->reference}, heb ik aan u een subsidie verleend van
    {$content->stage2->amount} voor de subsidieregeling borstprothesen transvrouwen (hierna: Subsidieregeling). In mijn
    administratie is uw aanvraag bekend onder nummer: {$content->reference}.</p>

<h2>Besluit</h2>

<p>Ik stel de subsidie vast op een lager bedrag dan aan u is verleend, namelijk € 0,00.</p>

<p>Mocht u in de toekomst alsnog gebruiken willen maken van de Subsidieregeling borstprothesen transvrouwen, dan dient u
    een volledig nieuwe aanvraag in te dienen, conform de vereisten die in artikel 7 van de regeling zijn opgenomen. U
    kunt zich niet beroepen op al eerder ingediende documenten.</p>

<h2>Hoe wordt de subsidie afgehandeld?</h2>

<p>De subsidie is geheel als voorschot aan u uitbetaald.</p>

<p>Het verschil tussen het vastgestelde subsidiebedrag en het ontvangen voorschot bedraagt {$content->stage2->amount}. Ik vorder dit
    verschil van u terug op grond van artikel 4:57 van de Algemene wet bestuursrecht.</p>

<p>Ik verzoek u het bedrag van {$content->stage2->amount} binnen zes weken na de datum van deze beschikking over te
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
    content_pdf  = e'{layout \'letter_layout.latte\'}

{block concern}
    Betreft: Vordering subsidie \'{$content->subsidyTitle}\'
{/block}

{block content}
    <p>Beste lezer,</p>

    <p>Bij brief van datum verleningsbrief, met kenmerk {$content->reference}, heb ik aan u een subsidie verleend van
        {$content->stage2->amount} voor de subsidieregeling borstprothesen transvrouwen (hierna: Subsidieregeling). In mijn
        administratie is uw aanvraag bekend onder nummer: {$content->reference}.</p>

    <h2>Besluit</h2>

    <p>Ik stel de subsidie vast op een lager bedrag dan aan u is verleend, namelijk € 0,00.</p>

    <p>Mocht u in de toekomst alsnog gebruiken willen maken van de Subsidieregeling borstprothesen transvrouwen, dan dient u
    een volledig nieuwe aanvraag in te dienen, conform de vereisten die in artikel 7 van de regeling zijn opgenomen. U
    kunt zich niet beroepen op al eerder ingediende documenten.</p>

    <h2>Hoe wordt de subsidie afgehandeld?</h2>

    <p>De subsidie is geheel als voorschot aan u uitbetaald.</p>

    <p>Het verschil tussen het vastgestelde subsidiebedrag en het ontvangen voorschot bedraagt {$content->stage2->amount}. Ik vorder dit
    verschil van u terug op grond van artikel 4:57 van de Algemene wet bestuursrecht.</p>

    <p>Ik verzoek u het bedrag van {$content->stage2->amount} binnen zes weken na de datum van deze beschikking over te
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

    <h4>Relatienummer</h4>
    <p>{$content->stage2->businessPartnerNumber}</p>

    <h4>Verplichtingennummer</h4>
    <p>{$content->stage2->liabilitiesNumber}</p>

    <p><i>Correspondentie uitsluitend richten aan het retouradres met vermelding van de datum het kenmerk van deze brief en met
        het onderwerp op de enveloppe.</i></p>
{/block}
',
    updated_at   = 'now()'
WHERE id = 'd9917011-3baf-4a5f-8b1f-0e8e2b62d0a4';

UPDATE public.subsidy_stage_transitions
SET
    condition                   = '{
        "type": "and",
        "conditions": [
            {
                "type": "or",
                "conditions": [
                    {
                        "type": "comparison",
                        "stage": 5,
                        "fieldCode": "assessment",
                        "operator": "===",
                        "value": "Vaststellen"
                    },
                    {
                        "type": "comparison",
                        "stage": 6,
                        "fieldCode": "assessment",
                        "operator": "===",
                        "value": "Vaststellen"
                    },
                    {
                        "type": "comparison",
                        "stage": 5,
                        "fieldCode": "assessment",
                        "operator": "===",
                        "value": "Vorderen"
                    },
                    {
                        "type": "comparison",
                        "stage": 6,
                        "fieldCode": "assessment",
                        "operator": "===",
                        "value": "Vorderen"
                    }
                ]
            },
            {
                "type": "comparison",
                "stage": 7,
                "fieldCode": "assessment",
                "operator": "===",
                "value": "Eens met de beoordeling op de vaststelling"
            },
            {
                "type": "comparison",
                "stage": 8,
                "fieldCode": "assessment",
                "operator": "===",
                "value": "Oneens met de beoordeling op de vaststelling"
            }
        ]
    }'
WHERE id = 'cd0491f3-9eef-4094-87fa-ae3babcacd04';
