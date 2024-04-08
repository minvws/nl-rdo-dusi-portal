INSERT INTO public.subsidy_stages (id, created_at, subsidy_version_id, title, subject_role, subject_organisation, stage,
                                   assessor_user_role, internal_note_field_code, allow_duplicate_assessors)
VALUES ('ef2238cf-a8ce-4376-ab2e-e821bc43ddb5', null, '513011cd-789b-4628-ba5c-2fee231f8959',
        'Informeren over verhoging van toegekend bedrag', 'assessor', null, 6, 'implementationCoordinator', null,
        false);

INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition, retention_period_on_approval, exclude_from_clone_data)
VALUES ('2c9e3924-b916-42e5-89d0-7e806fef1d26', 'Interne notitie', null, 'text', 'null', false,
        'increasedGrantInternalNote',
        'user', 'ef2238cf-a8ce-4376-ab2e-e821bc43ddb5', null, 'short', false);

INSERT INTO public.subsidy_stage_uis (id, subsidy_stage_id, version, status, input_ui, created_at, updated_at, view_ui)
VALUES ('0521b7fd-fd90-4fbe-af16-c2df2d21a0b0', 'ef2238cf-a8ce-4376-ab2e-e821bc43ddb5', 1, 'published', '{
    "type": "FormGroupControl",
    "options": {
        "section": true,
        "group": true
    },
    "elements": [
        {
            "type": "VerticalLayout",
            "elements": [
                {
                    "type": "CustomControl",
                    "scope": "#\/properties\/increasedGrantInternalNote",
                    "options": {
                        "format": "textarea"
                    }
                }
            ]
        }
    ]
}', null, null, '{
    "type": "FormGroupControl",
    "options": {
        "section": true
    },
    "elements": [
        {
            "type": "CustomControl",
            "scope": "#\/properties\/increasedGrantInternalNote",
            "options": {
                "readonly": true,
                "format": "textarea"
            }
        }
    ]
}');


UPDATE public.subsidy_stage_transitions
SET target_subsidy_stage_id = 'ef2238cf-a8ce-4376-ab2e-e821bc43ddb5'
WHERE id = 'a27195df-9825-4d18-acce-9b3492221d8a';

INSERT INTO public.subsidy_stage_transitions (id, current_subsidy_stage_id, target_subsidy_stage_id,
                                              target_application_status, condition, send_message, clone_data,
                                              assign_to_previous_assessor, description, expiration_period,
                                              evaluation_trigger)
VALUES ('2b493130-c191-4455-8de4-d932ab6c2b60', 'ef2238cf-a8ce-4376-ab2e-e821bc43ddb5', null, null, '{
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
            "stage": 5,
            "fieldCode": "implementationCoordinatorAssessment",
            "operator": "===",
            "value": "Goedgekeurd"
        }
    ]
}', true, false, false, 'Toegekend bedrag verhoogd', null, 'submit');

INSERT INTO public.subsidy_stage_transition_messages (id, subsidy_stage_transition_id, version, status, subject,
                                                      content_html, content_pdf, created_at, updated_at)
VALUES ('d3dcc915-fdaf-472a-9f3c-d9a09dc263b3', '2b493130-c191-4455-8de4-d932ab6c2b60', 1, 'published',
        'Verhoging toegewezen bedrag', e'{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de
        \'Regeling {$content->subsidyTitle}\' met referentienummer {$content->reference}.
        Met deze brief laat ik u weten dat het toegekende bedrag van is verhoogd naar € 24.000,-.
    </p>

    <h2>Wanneer ontvangt u de financiële ondersteuning?</h2>
    <p>U heeft reeds een deel van het toegekende bedrag ontvangen. Het restant bedrag van €9.000,- wordt in één keer
        uitbetaald. Wij streven ernaar dit bedrag binnen 10 werkdagen uit te keren.</p>

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
        de Minister van Volksgezondheid, Welzijn en Sport,<br/>
        namens deze,<br/>
        het afdelingshoofd van Dienst Uitvoering Subsidies aan Instellingen<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(\'vws_dusi_signature.jpg\')|dataStream}" />
        <br/>
        L. van der Weij
    </p>
{/block}
', e'{layout \'letter_layout.latte\'}

{block concern}
    Betreft: Verhoging toegewezen bedrag \'Regeling {$content->subsidyTitle}\'
{/block}

{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de \'Regeling {$content->subsidyTitle}\' met referentienummer {$content->reference}.
        Met deze brief laat ik u weten dat het toegekende bedrag van is verhoogd naar € 24.000,-.
    </p>

    <h2>Wanneer ontvangt u de financiële ondersteuning?</h2>
    <p>U heeft reeds een deel van het toegekende bedrag ontvangen. Het restant bedrag van €9.000 wordt in één keer uitbetaald. Wij streven ernaar de financiële
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
        de Minister van Volksgezondheid, Welzijn en Sport,<br/>
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
', '2024-04-05 08:59:24', null);
