INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id, required_condition, retention_period_on_approval, exclude_from_clone_data) VALUES ('74135e82-f8dc-4ae8-af6d-4dfa69ec08d7', 'Overschrijf vaststellings deadline', null, 'date', 'null', false, 'assignationDeadlineOverride', 'user', '1ec333d3-4b9c-437f-a04d-c1f6a7b70446', null, 'short', true);

INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id, required_condition, retention_period_on_approval, exclude_from_clone_data) VALUES ('1036f2d8-44f0-4c07-8e8a-bd068f132fc5', 'Vaststellings deadline', null, 'date', '{"readonly": true, "deadlineSource": "now", "deadlineAdditionalPeriod": "P1Y", "deadlineOverrideFieldReference": {"stage": 5, "fieldCode": "assignationDeadlineOverride"}}', false, 'assignationDeadline', 'calculated', '1ec333d3-4b9c-437f-a04d-c1f6a7b70446', '{"type": "comparison", "stage": 5, "value": "Uitstellen", "operator": "===", "fieldCode": "assessment"}', 'short', true);

UPDATE public.fields
SET required_condition = E'{"type": "comparison", "stage": 5, "value": "Vorderen", "operator": "===", "fieldCode": "assessment"}'
WHERE code = 'newAssignationAmount' AND subsidy_stage_id = '1ec333d3-4b9c-437f-a04d-c1f6a7b70446';

UPDATE public.fields
SET required_condition = E'{"type": "comparison", "stage": 5, "value": "Vorderen", "operator": "===", "fieldCode": "assessment"}'
WHERE code = 'reclaimAmount' AND subsidy_stage_id = '1ec333d3-4b9c-437f-a04d-c1f6a7b70446';

INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id, required_condition, retention_period_on_approval, exclude_from_clone_data) VALUES ('5157231e-2af8-455f-99ce-8d07b4a81114', 'Overschrijf vaststellings deadline', null, 'date', 'null', false, 'assignationDeadlineOverride', 'user', 'f5f76ce6-e2ed-4abf-a38e-103b8dfd2b82', null, 'short', true);

INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id, required_condition, retention_period_on_approval, exclude_from_clone_data) VALUES ('30fde98d-18cf-4a31-949f-fb49b716f3dc', 'Vaststellings deadline', null, 'date', '{"readonly": true, "deadlineSource": "now", "deadlineAdditionalPeriod": "P1Y", "deadlineOverrideFieldReference": {"stage": 6, "fieldCode": "assignationDeadlineOverride"}}', false, 'assignationDeadline', 'calculated', 'f5f76ce6-e2ed-4abf-a38e-103b8dfd2b82', '{"type": "comparison", "stage": 6, "value": "Uitstellen", "operator": "===", "fieldCode": "assessment"}', 'short', true);

UPDATE public.fields
SET required_condition = '{"type": "comparison", "stage": 6, "value": "Vorderen", "operator": "===", "fieldCode": "assessment"}'
WHERE code = 'newAssignationAmount' AND subsidy_stage_id = 'f5f76ce6-e2ed-4abf-a38e-103b8dfd2b82';

UPDATE public.fields
SET required_condition = '{"type": "comparison", "stage": 6, "value": "Vorderen", "operator": "===", "fieldCode": "assessment"}'
WHERE code = 'reclaimAmount' AND subsidy_stage_id = 'f5f76ce6-e2ed-4abf-a38e-103b8dfd2b82';

UPDATE public.fields
SET required_condition = '{"type": "or", "conditions": [{"type": "comparison", "stage": 5, "value": "Vorderen", "operator": "===", "fieldCode": "assessment"}, {"type": "comparison", "stage": 6, "value": "Vorderen", "operator": "===", "fieldCode": "assessment"}]}'
WHERE code = 'reclaimNumber' AND subsidy_stage_id = '0c2c1f22-624c-45fc-bb20-a3249b647fa7';

UPDATE public.subsidy_stage_transitions
SET target_application_review_deadline_source = 'application_submitted_at',
    target_application_review_deadline_additional_period = 'P74W'
WHERE id = '16f83400-7ff9-41ce-8ad7-040e316b8cee';

UPDATE public.subsidy_stage_transitions
SET target_application_review_deadline_source = 'field',
    target_application_review_deadline_source_field = '{"stage": 5, "fieldCode": "assignationDeadline"}'
WHERE id = '9055e316-e762-4776-b1fc-9e1c0f57c400';

UPDATE public.subsidy_stage_transitions
SET target_application_review_deadline_source = 'field',
    target_application_review_deadline_source_field = '{"stage": 6, "fieldCode": "assignationDeadline"}'
WHERE id = '739aecd5-4c03-424e-bf12-a7f3cecc7d94';

UPDATE public.subsidy_stage_uis
SET input_ui = '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Beoordeling","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/assessmentDelayChecklist","options":{"format":"checkbox-group"}}]}]},{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/assessment","options":{"format":"radio"}}]}]},{"type":"Group","label":"Deadline","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/assignationDeadlineOverride","options":{"placeholder":"","remoteAction":["onBlur"]}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/assignationDeadline","options":{"readonly":true}}]}],"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/assessment","schema":{"const":"Uitstellen"}}}},{"type":"Group","label":"Toelichting","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/proof","options":{"accept":"image\/jpeg,image\/png,application\/pdf","maxFileSize":20971520,"minItems":1,"maxItems":20,"tip":"Upload een of meerdere bewijsstukken. Toegestane bestandstypen: pdf, jpg, jpeg, png. Maximale bestandsgrootte: 20 MB."}},{"type":"CustomControl","scope":"#\/properties\/internalNote","options":{"format":"textarea"}}]}]},{"type":"Group","label":"Vorderen","elements":[{"type":"CustomControl","scope":"#\/properties\/motivation","options":{"format":"textarea"}},{"type":"CustomControl","scope":"#\/properties\/newAssignationAmount","options":{"format":"float"}},{"type":"CustomControl","scope":"#\/properties\/reclaimAmount","options":{"format":"float"}}],"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/assessment","schema":{"const":"Vorderen"}}}}]}',
    view_ui = '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"FormGroupControl","label":"Beoordeling","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/assessmentDelayChecklist","options":{"readonly":true,"format":"checkbox-group"}}]},{"type":"CustomControl","scope":"#\/properties\/assessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/assignationDeadlineOverride","options":{"readonly":true},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/assessment","schema":{"const":"Uitstellen"}}}},{"type":"CustomControl","scope":"#\/properties\/assignationDeadline","options":{"readonly":true},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/assessment","schema":{"const":"Uitstellen"}}}},{"type":"FormResultsTable","options":{"fields":{"Bewijs":"{proof}","Interne notitie":"{internalNote}"},"showEmptyFields":true}},{"type":"FormGroupControl","label":"Vorderen","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Motivatie":"{motivation}","Vastgesteld bedrag":"{newAssignationAmount}","Te vorderen bedrag":"{reclaimAmount}"}},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/assessment","schema":{"const":"Vorderen"}}}}]}]}'
WHERE id = 'ef196de1-5c15-4af3-9ec8-046ca4419fd1';

UPDATE public.subsidy_stage_uis
SET input_ui = '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Beoordeling","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/assessmentDelayChecklist","options":{"format":"checkbox-group"}}]}]},{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/assessment","options":{"format":"radio"}}]}]},{"type":"Group","label":"Deadline","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/assignationDeadlineOverride","options":{"placeholder":"","remoteAction":["onBlur"]}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/assignationDeadline","options":{"readonly":true}}]}],"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/assessment","schema":{"const":"Uitstellen"}}}},{"type":"Group","label":"Toelichting","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/proof","options":{"accept":"image\/jpeg,image\/png,application\/pdf","maxFileSize":20971520,"minItems":1,"maxItems":20,"tip":"Upload een of meerdere bewijsstukken. Toegestane bestandstypen: pdf, jpg, jpeg, png. Maximale bestandsgrootte: 20 MB."}},{"type":"CustomControl","scope":"#\/properties\/internalNote","options":{"format":"textarea"}}]}]},{"type":"Group","label":"Vorderen","elements":[{"type":"CustomControl","scope":"#\/properties\/motivation","options":{"format":"textarea"}},{"type":"CustomControl","scope":"#\/properties\/newAssignationAmount","options":{"format":"float"}},{"type":"CustomControl","scope":"#\/properties\/reclaimAmount","options":{"format":"float"}}],"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/assessment","schema":{"const":"Vorderen"}}}}]}',
    view_ui = '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"FormGroupControl","label":"Beoordeling","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/assessmentDelayChecklist","options":{"readonly":true,"format":"checkbox-group"}}]},{"type":"CustomControl","scope":"#\/properties\/assessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/assignationDeadlineOverride","options":{"readonly":true},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/assessment","schema":{"const":"Uitstellen"}}}},{"type":"CustomControl","scope":"#\/properties\/assignationDeadline","options":{"readonly":true},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/assessment","schema":{"const":"Uitstellen"}}}},{"type":"FormResultsTable","options":{"fields":{"Bewijs":"{proof}","Interne notitie":"{internalNote}"},"showEmptyFields":true}},{"type":"FormGroupControl","label":"Vorderen","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Motivatie":"{motivation}","Vastgesteld bedrag":"{newAssignationAmount}","Te vorderen bedrag":"{reclaimAmount}"}},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/assessment","schema":{"const":"Vorderen"}}}}]}]}'
WHERE id = 'd15ff747-b912-4abc-b6df-2a750c820d92';

UPDATE public.subsidy_stage_transition_messages
SET content_html = '{layout ''letter_view_layout.latte''}

{block content}
    <p>Beste lezer,</p>

    <p>Bij brief van {$content->lastAllocatedAt|date:''d-m-Y''}, met kenmerk {$content->reference}, heb ik aan u een subsidie verleend van
        {$content->stage2->amount} voor de subsidieregeling borstprothesen transvrouwen (hierna: Subsidieregeling). In mijn
        administratie is uw aanvraag bekend onder nummer: {$content->reference}.</p>

    {if $content->stage5?->motivation}
        <p>{$content->stage5->motivation}</p>
        {else}
            {if $content->stage6?->motivation}
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
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        P.A. van Hecking Colenbrander
    </p>
{/block}
',
    content_pdf = '{layout ''letter_layout.latte''}

{block concern}
    Betreft: Vordering subsidie ''{$content->subsidyTitle}''
{/block}

{block content}
    <p>Beste lezer,</p>

    <p>Bij brief van {$content->lastAllocatedAt|date:''d-m-Y''}, met kenmerk {$content->reference}, heb ik aan u een subsidie verleend van
        {$content->stage2->amount} voor de subsidieregeling borstprothesen transvrouwen (hierna: Subsidieregeling). In mijn
        administratie is uw aanvraag bekend onder nummer: {$content->reference}.</p>

    {if $content->stage5?->motivation}
        <p>{$content->stage5->motivation}</p>
        {else}
            {if $content->stage6?->motivation}
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
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
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
    updated_at = 'now()'
WHERE id = 'b8cfccbc-d9ba-463f-8cbc-4930057a0dff';

