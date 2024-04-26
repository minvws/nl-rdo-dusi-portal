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
    updated_at = 'now()',
    view_ui    = '{
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
    updated_at = 'now()',
    view_ui    = '{
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
    updated_at = 'now()',
    view_ui    = '{
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

INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition, retention_period_on_approval, exclude_from_clone_data)
VALUES ('288680cd-086c-42c3-becd-c275df9d49ea', 'HBO vooropleiding dans en muziek', null, 'select', '{
    "default": null,
    "options": [
        "Academie voor Theater en Dans: Nationale Balletacademie (dans) (21QA)",
        "Codarts (dans) (14NI)",
        "Koninklijk Conservatorium (dans en muziek) (23KJ)]"
    ]
}', false, 'hboPreviousEducationPrimary', 'user', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', null, 'short', false);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition, retention_period_on_approval, exclude_from_clone_data)
VALUES ('e128f786-a5a1-4d89-9f32-02f970ff615f', 'HBO vooropleiding dans en muziek', null, 'select', '{
    "default": null,
    "options": [
        "Academie voor Theater en Dans 5 o’clock class (dans) (21QA)",
        "Academie voor Theater en Dans: Nationale Balletacademie (dans) (21QA)",
        "ArtEz (dans) (27NF)",
        "Codarts (dans) (14NI)",
        "Codarts (dans en muziek) (14NI)",
        "Conservatorium Maastricht (muziek) (25JX)",
        "Conservatorium van Amsterdam (Muziek) (21QA)",
        "Fontys Hogeschool voor de Kunsten (dans) (30GB)",
        "Fontys Hogeschool voor de Kunsten (musical) (30GB)",
        "Koninklijk Conservatorium (dans en muziek) (23KJ)",
        "Lucia Marthas Institute for Performing Arts (dans) (25LW)",
        "Prins Claus Conservatorium (muziek) (25BE)"
    ]
}', false, 'hboPreviousEducationSecondary', 'user', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8', null, 'short', false);


UPDATE public.fields
SET params = '{
    "options": [
        "Valt de aanvrager onder de WSNP/bewindvoering?",
        "Is het subsidiebedrag juist vermeld in SAP?",
        "Komt het IBAN op de aanvraag overeen met SAP?",
        "Is de aangemaakte verplichting geboekt op juiste budgetplaats en budgetpositie?"
    ]
}'
WHERE id = '0575d1af-dcbf-49ea-b4c6-5d135a03f15f';

UPDATE public.subsidy_stage_transition_messages
SET content_html = e'{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.
    </p>

    <h2>Besluit</h2>
    <p>Hierbij ken ik uw aanvraag (gedeeltelijk) toe en stel ik de subsidie (aangepast) vast
            op € {formatCurrency($content->stage3->amount)}. U vroeg een subsidie aan
            van € {formatCurrency($content->stage1->requestedSubsidyAmount)}.</p>

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
',
    content_pdf  = e'{layout \'letter_layout.latte\'}

{block concern}
Betreft: Verlening aanvraag {$content->subsidyTitle}
{/block}

{block content}
<p>Beste lezer,</p>
<p>
    Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.
</p>

<h2>Besluit</h2>
<p>Hierbij ken ik uw aanvraag (gedeeltelijk) toe en stel ik de subsidie (aangepast) vast
        op € {formatCurrency($content->stage3->amount)}. U vroeg een subsidie aan
        van € {formatCurrency($content->stage1->requestedSubsidyAmount)}.</p>

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
    <li>Wet overige OCW-subsidies BWBR0009458;</li>
    <li>Kaderregeling subsidies OCW, SZW en VWS;</li>
    {if $content->stage1->educationType === \'Primair onderwijs\'}
    <li>Subsidieregeling reiskosten DAMU-leerlingen, nr.24900503;</li>
    {else}
    <li>Subsidieregeling reiskosten DAMU-leerlingen, nr. VO/1360431</li>
    {/if}
    <li>Algemene wet bestuursrecht;</li>
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
',
    updated_at   = 'now()'
WHERE id = '9445db1e-2aeb-4434-be02-e57622c28e77';

UPDATE public.subsidy_stage_uis
SET input_ui         = '{
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
                                            },
                                            {
                                                "type": "CustomControl",
                                                "scope": "#\/properties\/hboPreviousEducationPrimary",
                                                "label": "HBO vooropleiding",
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
                                                "scope": "#\/properties\/hboPreviousEducationSecondary",
                                                "label": "HBO vooropleiding",
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
                                                    "maxFileSize": 20971520,
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
                                                    "maxFileSize": 20971520,
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
                                                "label": "Inschrijfbewijs hbo-vooropleiding dans en muziek",
                                                "options": {
                                                    "accept": "image\/jpeg,image\/png,.pdf",
                                                    "maxFileSize": 20971520,
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
                                "Naam rekeninghouder": "{bankAccountHolder}",
                                "Bankafschrift (indien nodig)": "{bankStatement}"
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
                    },
                    {
                        "type": "FormResultsTable",
                        "options": {
                            "fields": {
                                "HBO vooropleiding": "{hboPreviousEducationPrimary}"
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
                                "HBO vooropleiding": "{hboPreviousEducationSecondary}"
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


-- START DAMU assessment field changes
