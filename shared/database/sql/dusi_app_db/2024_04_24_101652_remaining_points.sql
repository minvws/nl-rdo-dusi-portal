UPDATE public.fields
SET params = '{
    "default": null,
    "options": [
        "Eens met de eerste beoordeling",
        "Oneens met de eerste beoordeling"
    ]
}'
WHERE code = 'secondAssessment'
  AND subsidy_stage_id = 'b2b08566-8493-4560-8afa-d56402931f74';

UPDATE public.subsidy_stage_transitions
SET assign_to_previous_assessor = true
WHERE id = 'cd0491f3-9eef-4094-87fa-ae3babcacd04';

-- AIGT assessment field changes

INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition, retention_period_on_approval, exclude_from_clone_data)
VALUES ('8b606c86-0cca-45e4-a9a1-21c9bf52b665', 'Controlevragen', null, 'multiselect', '{
    "options": [
        "Akkoord met de vaststelling",
        "De verplichting is geaccordeerd in SAP",
        "De vaststellingsbrief mag verzonden worden"
    ]
}', false, 'assignationImplementationAssessmentChecklist', 'user', '051364be-fa12-4af7-a1b8-c80f5e9dd652', null,
        'short', false);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition, retention_period_on_approval, exclude_from_clone_data)
VALUES ('1ed7c7ef-6a37-407c-a931-d4afb08ce115', 'Controlevragen', null, 'multiselect', '{
    "options": [
        "OIGT heeft de afronding van de buitenlandstage bevestigd",
        "De verplichting is vastgesteld"
    ]
}', false, 'assignationAssessmentChecklist', 'user', '59ddbc42-8ffc-4e2c-a751-d937714b6df6', null, 'short', false);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition, retention_period_on_approval, exclude_from_clone_data)
VALUES ('b391e3e3-0cf5-4956-ae78-3ab0435f3874', 'Controlevragen', null, 'multiselect', '{
    "options": [
        "OIGT heeft de afronding van de buitenlandstage bevestigd",
        "De verplichting is vastgesteld"
    ]
}', false, 'assignationAssessmentChecklist', 'user', '2b06aee1-ea36-41a4-b7ae-74fa53c64a64', null, 'short', false);

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
                                "scope": "#\/properties\/assignationAssessmentChecklist",
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
    updated_at       = 'now()',
    view_ui          = '{
        "type": "FormGroupControl",
        "options": {
            "section": true
        },
        "elements": [
            {
                "type": "CustomControl",
                "scope": "#\/properties\/assignationAssessmentChecklist",
                "options": {
                    "readonly": true,
                    "format": "checkbox-group"
                }
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
                        "Bewijs": "{proof}"
                    }
                }
            },
            {
                "type": "CustomControl",
                "scope": "#\/properties\/internalNote",
                "options": {
                    "readonly": true,
                    "format": "textarea"
                }
            }
        ]
    }'
WHERE id = '6b9e3359-8c44-4bfd-a593-baa5c4b8d19d';

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
                                "scope": "#\/properties\/assignationAssessmentChecklist",
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
    updated_at       = 'now()',
    view_ui          = '{
        "type": "FormGroupControl",
        "options": {
            "section": true
        },
        "elements": [
            {
                "type": "CustomControl",
                "scope": "#\/properties\/assignationAssessmentChecklist",
                "options": {
                    "readonly": true,
                    "format": "checkbox-group"
                }
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
                        "Bewijs": "{proof}"
                    }
                }
            },
            {
                "type": "CustomControl",
                "scope": "#\/properties\/internalNote",
                "options": {
                    "readonly": true,
                    "format": "textarea"
                }
            }
        ]
    }'
WHERE id = '2a227775-700d-4f59-9322-900bb326afff';

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
                                "scope": "#\/properties\/assignationImplementationAssessmentChecklist",
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
                "type": "CustomControl",
                "scope": "#\/properties\/assignationImplementationAssessmentChecklist",
                "options": {
                    "readonly": true,
                    "format": "checkbox-group"
                }
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
                "type": "CustomControl",
                "scope": "#\/properties\/internalNote",
                "options": {
                    "readonly": true,
                    "format": "textarea"
                }
            }
        ]
    }'
WHERE id = '9fb35125-318e-4426-8857-facefdd94fee';

-- END AIGT assessment field changes

-- START DAMU assessment field changes

UPDATE public.fields
SET params                       = '{
        "options": [
            "Valt de aanvrager onder de WSNP/bewindvoering?",
            "Is het subsidiebedrag juist vermeld in SAP?",
            "Komt het IBAN op de aanvraag overeen met SAP?",
            "Is de aangemaakte verplichting geboekt op juiste budgetplaats en budgetpositie?"
        ]
    }'
WHERE id = '0575d1af-dcbf-49ea-b4c6-5d135a03f15f';

UPDATE public.subsidy_stage_transition_messages
SET
content_html = e'{block content}
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
    <p>Ik streef ernaar het toegekende subsidiebedrag zo spoedig mogelijk naar u over te maken onder vermelding van
        het referentienummer {$content->reference}.</p>
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
', content_pdf = e'{layout \'letter_layout.latte\'}

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
<p>Ik streef ernaar het toegekende subsidiebedrag zo spoedig mogelijk naar u over te maken onder vermelding van
    het referentienummer {$content->reference}.</p>

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
', created_at = '2024-04-25 07:38:56', updated_at = null WHERE id = '9445db1e-2aeb-4434-be02-e57622c28e77';


-- START DAMU assessment field changes
