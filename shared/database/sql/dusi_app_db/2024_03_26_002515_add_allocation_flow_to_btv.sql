DELETE FROM public.subsidy_stage_transition_messages WHERE id = '1983fa28-cfc6-4c0f-9bc3-cba9e0909456';
DELETE FROM public.subsidy_stage_transitions WHERE id = '5b876216-ba37-4b13-aa99-e311db027d6b';

UPDATE public.subsidy_stage_transitions set description = 'Interne controle is het oneens met de eerste beoordeling' where id = '0be7031b-c841-4c27-8104-2d2676d32cff';
UPDATE public.subsidy_stage_transitions set description = 'Interne controle is het eens met de afkeuring' where id = '3a293e03-1de4-47bf-917b-841b7c0a1fff';

INSERT INTO public.subsidy_stages (id, created_at, subsidy_version_id, title, subject_role, subject_organisation, stage, assessor_user_role, internal_note_field_code, allow_duplicate_assessors) VALUES ('1ec333d3-4b9c-437f-a04d-c1f6a7b70446', null, '907bb399-0d19-4e1a-ac75-25a864df27c6', 'Wachten op vaststelling', 'assessor', null, 5, 'assessor', 'InternalNote', true);
INSERT INTO public.subsidy_stages (id, created_at, subsidy_version_id, title, subject_role, subject_organisation, stage, assessor_user_role, internal_note_field_code, allow_duplicate_assessors) VALUES ('f5f76ce6-e2ed-4abf-a38e-103b8dfd2b82', null, '907bb399-0d19-4e1a-ac75-25a864df27c6', 'Vaststellings controle', 'assessor', null, 6, 'assessor', 'InternalNote', true);
INSERT INTO public.subsidy_stages (id, created_at, subsidy_version_id, title, subject_role, subject_organisation, stage, assessor_user_role, internal_note_field_code, allow_duplicate_assessors) VALUES ('0c2c1f22-624c-45fc-bb20-a3249b647fa7', null, '907bb399-0d19-4e1a-ac75-25a864df27c6', 'Interne vaststellings controle', 'assessor', null, 7, 'internalAuditor', 'InternalNote', true);
INSERT INTO public.subsidy_stages (id, created_at, subsidy_version_id, title, subject_role, subject_organisation, stage, assessor_user_role, internal_note_field_code, allow_duplicate_assessors) VALUES ('1916dc00-39f8-4bd8-a4f3-a471fb5ef3a7', null, '907bb399-0d19-4e1a-ac75-25a864df27c6', 'Uitvoeringscoördinator vaststellings controle', 'assessor', null, 8, 'implementationCoordinator', 'InternalNote', true);

INSERT INTO public.subsidy_stage_transitions (id, current_subsidy_stage_id, target_subsidy_stage_id, target_application_status, condition, send_message, clone_data, assign_to_previous_assessor, description, expiration_period, evaluation_trigger) VALUES ('16f83400-7ff9-41ce-8ad7-040e316b8cee', 'e456e790-1919-4a2b-b3d5-337d0053abe3', '1ec333d3-4b9c-437f-a04d-c1f6a7b70446', 'allocated', '{"type":"and","conditions":[{"type":"comparison","stage":2,"fieldCode":"firstAssessment","operator":"===","value":"Goedgekeurd"},{"type":"comparison","stage":4,"fieldCode":"internalAssessment","operator":"===","value":"Eens met de eerste beoordeling"}]}', true, false, false, 'Interne controle is het eens met de goedkeuring', 364, 'submit');
INSERT INTO public.subsidy_stage_transitions (id, current_subsidy_stage_id, target_subsidy_stage_id, target_application_status, condition, send_message, clone_data, assign_to_previous_assessor, description, expiration_period, evaluation_trigger) VALUES ('ec046c05-8804-4af4-9fbc-4390b2c52bce', '1ec333d3-4b9c-437f-a04d-c1f6a7b70446', '0c2c1f22-624c-45fc-bb20-a3249b647fa7', null, '{"type":"in","stage":5,"fieldCode":"assessment","values":["Vaststellen","Vorderen"]}', false, false, false, 'Voortijdige vaststellings beoordeling voltooid', null, 'submit');
INSERT INTO public.subsidy_stage_transitions (id, current_subsidy_stage_id, target_subsidy_stage_id, target_application_status, condition, send_message, clone_data, assign_to_previous_assessor, description, expiration_period, evaluation_trigger) VALUES ('a1dbb58a-5643-4424-a1be-5839b85980fb', '1ec333d3-4b9c-437f-a04d-c1f6a7b70446', 'f5f76ce6-e2ed-4abf-a38e-103b8dfd2b82', null, null, false, false, false, 'Vaststellings periode voltooid', null, 'expiration');
INSERT INTO public.subsidy_stage_transitions (id, current_subsidy_stage_id, target_subsidy_stage_id, target_application_status, condition, send_message, clone_data, assign_to_previous_assessor, description, expiration_period, evaluation_trigger) VALUES ('9055e316-e762-4776-b1fc-9e1c0f57c400', '1ec333d3-4b9c-437f-a04d-c1f6a7b70446', '1ec333d3-4b9c-437f-a04d-c1f6a7b70446', null, '{"type":"comparison","stage":5,"fieldCode":"assessment","operator":"===","value":"Uitstellen"}', false, false, false, 'Voortijdige vaststelling uitstellen', 364, 'submit');
INSERT INTO public.subsidy_stage_transitions (id, current_subsidy_stage_id, target_subsidy_stage_id, target_application_status, condition, send_message, clone_data, assign_to_previous_assessor, description, expiration_period, evaluation_trigger) VALUES ('d336a82a-f7e3-461b-a1cf-ffa7a1d7bf9b', 'f5f76ce6-e2ed-4abf-a38e-103b8dfd2b82', '0c2c1f22-624c-45fc-bb20-a3249b647fa7', null, '{"type":"in","stage":6,"fieldCode":"assessment","values":["Vaststellen","Vorderen"]}', false, false, false, 'Vaststellings beoordeling voltooid', null, 'submit');
INSERT INTO public.subsidy_stage_transitions (id, current_subsidy_stage_id, target_subsidy_stage_id, target_application_status, condition, send_message, clone_data, assign_to_previous_assessor, description, expiration_period, evaluation_trigger) VALUES ('739aecd5-4c03-424e-bf12-a7f3cecc7d94', 'f5f76ce6-e2ed-4abf-a38e-103b8dfd2b82', '1ec333d3-4b9c-437f-a04d-c1f6a7b70446', null, '{"type":"comparison","stage":6,"fieldCode":"assessment","operator":"===","value":"Uitstellen"}', false, true, true, 'Vaststelling uitstellen', 364, 'submit');
INSERT INTO public.subsidy_stage_transitions (id, current_subsidy_stage_id, target_subsidy_stage_id, target_application_status, condition, send_message, clone_data, assign_to_previous_assessor, description, expiration_period, evaluation_trigger) VALUES ('f59b5798-b2a7-44f0-8301-6c5e50a75194', '0c2c1f22-624c-45fc-bb20-a3249b647fa7', '1916dc00-39f8-4bd8-a4f3-a471fb5ef3a7', null, '{"type":"and","conditions":[{"type":"or","conditions":[{"type":"in","stage":5,"fieldCode":"assessment","values":["Vaststellen","Vorderen"]},{"type":"in","stage":6,"fieldCode":"assessment","values":["Vaststellen","Vorderen"]}]},{"type":"comparison","stage":7,"fieldCode":"assessment","operator":"===","value":"Eens met de beoordeling op de vaststelling"}]}', false, false, false, 'Interne controle is het eens met de vaststellingsbeoordeling', null, 'submit');
INSERT INTO public.subsidy_stage_transitions (id, current_subsidy_stage_id, target_subsidy_stage_id, target_application_status, condition, send_message, clone_data, assign_to_previous_assessor, description, expiration_period, evaluation_trigger) VALUES ('a47e0ae1-74a9-4522-8784-a4ee76e670a6', '0c2c1f22-624c-45fc-bb20-a3249b647fa7', 'f5f76ce6-e2ed-4abf-a38e-103b8dfd2b82', null, '{"type":"and","conditions":[{"type":"or","conditions":[{"type":"in","stage":5,"fieldCode":"assessment","values":["Vaststellen","Vorderen"]},{"type":"in","stage":6,"fieldCode":"assessment","values":["Vaststellen","Vorderen"]}]},{"type":"comparison","stage":7,"fieldCode":"assessment","operator":"===","value":"Oneens met de beoordeling op de vaststelling"}]}', false, false, false, 'Interne controle oneens met de vaststellingsbeoordeling', null, 'submit');
INSERT INTO public.subsidy_stage_transitions (id, current_subsidy_stage_id, target_subsidy_stage_id, target_application_status, condition, send_message, clone_data, assign_to_previous_assessor, description, expiration_period, evaluation_trigger) VALUES ('3ad4bbc8-4810-4d57-a004-f059a930df17', '1916dc00-39f8-4bd8-a4f3-a471fb5ef3a7', null, 'reclaimed', '{"type":"and","conditions":[{"type":"or","conditions":[{"type":"comparison","stage":5,"fieldCode":"assessment","operator":"===","value":"Vorderen"},{"type":"comparison","stage":6,"fieldCode":"assessment","operator":"===","value":"Vorderen"}]},{"type":"comparison","stage":7,"fieldCode":"assessment","operator":"===","value":"Eens met de beoordeling op de vaststelling"},{"type":"comparison","stage":8,"fieldCode":"assessment","operator":"===","value":"Eens met de beoordeling op de vaststelling"}]}', true, false, false, 'Uitvoeringscoördinator is het eens met de vordering', null, 'submit');
INSERT INTO public.subsidy_stage_transitions (id, current_subsidy_stage_id, target_subsidy_stage_id, target_application_status, condition, send_message, clone_data, assign_to_previous_assessor, description, expiration_period, evaluation_trigger) VALUES ('22372aab-995d-4f68-a9e2-c899217eac88', '1916dc00-39f8-4bd8-a4f3-a471fb5ef3a7', null, 'approved', '{"type":"and","conditions":[{"type":"or","conditions":[{"type":"comparison","stage":5,"fieldCode":"assessment","operator":"===","value":"Vaststellen"},{"type":"comparison","stage":6,"fieldCode":"assessment","operator":"===","value":"Vaststellen"}]},{"type":"comparison","stage":7,"fieldCode":"assessment","operator":"===","value":"Eens met de beoordeling op de vaststelling"},{"type":"comparison","stage":8,"fieldCode":"assessment","operator":"===","value":"Eens met de beoordeling op de vaststelling"}]}', true, false, false, 'Uitvoeringscoördinator is het eens met de vaststellingsbeoordeling', null, 'submit');
INSERT INTO public.subsidy_stage_transitions (id, current_subsidy_stage_id, target_subsidy_stage_id, target_application_status, condition, send_message, clone_data, assign_to_previous_assessor, description, expiration_period, evaluation_trigger) VALUES ('cd0491f3-9eef-4094-87fa-ae3babcacd04', '1916dc00-39f8-4bd8-a4f3-a471fb5ef3a7', 'f5f76ce6-e2ed-4abf-a38e-103b8dfd2b82', null, '{"type":"and","conditions":[{"type":"or","conditions":[{"type":"comparison","stage":5,"fieldCode":"assessment","operator":"===","value":"Vaststellen"},{"type":"comparison","stage":6,"fieldCode":"assessment","operator":"===","value":"Vaststellen"}]},{"type":"comparison","stage":7,"fieldCode":"assessment","operator":"===","value":"Eens met de beoordeling op de vaststelling"},{"type":"comparison","stage":8,"fieldCode":"assessment","operator":"===","value":"Oneens met de beoordeling op de vaststelling"}]}', false, false, false, 'Uitvoeringscoördinator is het oneens met de vaststellingsbeoordeling', null, 'submit');

INSERT INTO public.subsidy_stage_uis (id, subsidy_stage_id, version, status, input_ui, created_at, updated_at, view_ui) VALUES ('ef196de1-5c15-4af3-9ec8-046ca4419fd1', '1ec333d3-4b9c-437f-a04d-c1f6a7b70446', 1, 'published', '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/assessment","options":{"format":"radio"}}]}]},{"type":"Group","label":"Toelichting","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/proof","label":"Bewijs","options":{"accept":"image\/jpeg,image\/png,application\/pdf","maxFileSize":20971520,"minItems":1,"maxItems":20,"tip":"Upload een of meerdere bewijsstukken. Toegestane bestandstypen: pdf, jpg, jpeg, png. Maximale bestandsgrootte: 20 MB."}},{"type":"CustomControl","scope":"#\/properties\/internalNote","options":{"format":"textarea"}}]}]}]}', null, null, '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"CustomControl","scope":"#\/properties\/assessment","options":{"readonly":true,"format":"radio"}},{"type":"FormResultsTable","options":{"fields":{"Bewijs":"{proof}"}}},{"type":"CustomControl","scope":"#\/properties\/internalNote","options":{"readonly":true,"format":"textarea"}}]}');
INSERT INTO public.subsidy_stage_uis (id, subsidy_stage_id, version, status, input_ui, created_at, updated_at, view_ui) VALUES ('d15ff747-b912-4abc-b6df-2a750c820d92', 'f5f76ce6-e2ed-4abf-a38e-103b8dfd2b82', 1, 'published', '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/assessment","options":{"format":"radio"}}]}]},{"type":"Group","label":"Toelichting","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/proof","label":"Bewijs","options":{"accept":"image\/jpeg,image\/png,application\/pdf","maxFileSize":20971520,"minItems":1,"maxItems":20,"tip":"Upload een of meerdere bewijsstukken. Toegestane bestandstypen: pdf, jpg, jpeg, png. Maximale bestandsgrootte: 20 MB."}},{"type":"CustomControl","scope":"#\/properties\/internalNote","options":{"format":"textarea"}}]}]}]}', null, null, '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"CustomControl","scope":"#\/properties\/assessment","options":{"readonly":true,"format":"radio"}},{"type":"FormResultsTable","options":{"fields":{"Bewijs":"{proof}"}}},{"type":"CustomControl","scope":"#\/properties\/internalNote","options":{"readonly":true,"format":"textarea"}}]}');
INSERT INTO public.subsidy_stage_uis (id, subsidy_stage_id, version, status, input_ui, created_at, updated_at, view_ui) VALUES ('fe5b6562-9cf9-4c2f-b963-dadc87044766', '0c2c1f22-624c-45fc-bb20-a3249b647fa7', 1, 'published', '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/assessment","options":{"format":"radio"}}]}]},{"type":"Group","label":"Toelichting","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/internalNote","options":{"format":"textarea"}}]}]}]}', null, null, '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"CustomControl","scope":"#\/properties\/assessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/internalNote","options":{"readonly":true,"format":"textarea"}}]}');
INSERT INTO public.subsidy_stage_uis (id, subsidy_stage_id, version, status, input_ui, created_at, updated_at, view_ui) VALUES ('08cdcb36-d618-4e89-8fb3-778e66a3bf2a', '1916dc00-39f8-4bd8-a4f3-a471fb5ef3a7', 1, 'published', '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/assessment","options":{"format":"radio"}}]}]},{"type":"Group","label":"Toelichting","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/internalNote","options":{"format":"textarea"}}]}]}]}', null, null, '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"CustomControl","scope":"#\/properties\/assessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/internalNote","options":{"readonly":true,"format":"textarea"}}]}');

INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id, required_condition, retention_period_on_approval, exclude_from_clone_data) VALUES ('e955434e-5597-46a6-baec-ffafa78daca2', 'Beoordeling', null, 'select', '{"default": null, "options": ["Vaststellen", "Vorderen", "Uitstellen"]}', true, 'assessment', 'user', '1ec333d3-4b9c-437f-a04d-c1f6a7b70446', null, 'short', false);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id, required_condition, retention_period_on_approval, exclude_from_clone_data) VALUES ('f8394b5c-54ac-4743-8859-c8197fbe5de0', 'Interne notitie', null, 'text', 'null', false, 'internalNote', 'user', '1ec333d3-4b9c-437f-a04d-c1f6a7b70446', null, 'short', false);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id, required_condition, retention_period_on_approval, exclude_from_clone_data) VALUES ('e7927aef-07aa-48c2-b375-64ea22afce3f', 'Bewijsstukken', null, 'upload', '{"maxItems": 20, "minItems": 1, "mimeTypes": ["image/jpeg", "image/png", "application/pdf"], "maxFileSize": 20971520}', false, 'proof', 'user', '1ec333d3-4b9c-437f-a04d-c1f6a7b70446', null, 'short', false);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id, required_condition, retention_period_on_approval, exclude_from_clone_data) VALUES ('10090a10-09a2-4993-9125-2d7aff4e4206', 'Beoordeling', null, 'select', '{"default": null, "options": ["Vaststellen", "Vorderen", "Uitstellen"]}', true, 'assessment', 'user', 'f5f76ce6-e2ed-4abf-a38e-103b8dfd2b82', null, 'short', false);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id, required_condition, retention_period_on_approval, exclude_from_clone_data) VALUES ('76f80d97-5405-41b2-84d6-279fa27a69a5', 'Interne notitie', null, 'text', 'null', false, 'internalNote', 'user', 'f5f76ce6-e2ed-4abf-a38e-103b8dfd2b82', null, 'short', false);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id, required_condition, retention_period_on_approval, exclude_from_clone_data) VALUES ('c12c1c8e-bd68-4be4-9b81-fa1d2cb35c0b', 'Bewijsstukken', null, 'upload', '{"maxItems": 20, "minItems": 1, "mimeTypes": ["image/jpeg", "image/png", "application/pdf"], "maxFileSize": 20971520}', false, 'proof', 'user', 'f5f76ce6-e2ed-4abf-a38e-103b8dfd2b82', null, 'short', false);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id, required_condition, retention_period_on_approval, exclude_from_clone_data) VALUES ('957621d3-05ac-4e90-81e9-98750d75c541', 'Beoordeling', null, 'select', '{"default": null, "options": ["Eens met de beoordeling op de vaststelling", "Oneens met de beoordeling op de vaststelling"]}', true, 'assessment', 'user', '0c2c1f22-624c-45fc-bb20-a3249b647fa7', null, 'short', false);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id, required_condition, retention_period_on_approval, exclude_from_clone_data) VALUES ('e537583c-2ab5-49b7-914e-239f5985874d', 'Interne notitie', null, 'text', 'null', false, 'internalNote', 'user', '0c2c1f22-624c-45fc-bb20-a3249b647fa7', null, 'short', false);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id, required_condition, retention_period_on_approval, exclude_from_clone_data) VALUES ('46876853-872c-45fb-879c-59ce87f1de2c', 'Beoordeling', null, 'select', '{"default": null, "options": ["Eens met de beoordeling op de vaststelling", "Oneens met de beoordeling op de vaststelling"]}', true, 'assessment', 'user', '1916dc00-39f8-4bd8-a4f3-a471fb5ef3a7', null, 'short', false);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id, required_condition, retention_period_on_approval, exclude_from_clone_data) VALUES ('6254dc3b-51e6-4906-bd4e-b46487a03935', 'Interne notitie', null, 'text', 'null', false, 'internalNote', 'user', '1916dc00-39f8-4bd8-a4f3-a471fb5ef3a7', null, 'short', false);

INSERT INTO public.subsidy_stage_transition_messages (id, subsidy_stage_transition_id, version, status, subject, content_html, content_pdf, created_at, updated_at) VALUES ('532d7372-a029-4190-bf8f-c8417ce9acb4', '16f83400-7ff9-41ce-8ad7-040e316b8cee', 1, 'published', 'Aanvraag goedgekeurd', e'{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.
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
', e'{layout \'letter_layout.latte\'}

{block concern}
    Betreft: Verlening aanvraag {$content->subsidyTitle}
{/block}

{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.
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

    <h4>Verplichtingennummer</h4>
    <p>{$content->stage2->liabilitiesNumber}</p>
{/block}
', '2024-03-25 23:19:33', null);
INSERT INTO public.subsidy_stage_transition_messages (id, subsidy_stage_transition_id, version, status, subject, content_html, content_pdf, created_at, updated_at) VALUES ('d8c2a8d1-e512-40a1-94f8-6535cc85289c', '22372aab-995d-4f68-a9e2-c899217eac88', 1, 'published', 'Aanvraag vastgesteld', e'{block content}
    <p>Beste lezer,</p>
    <p>Met mijn brief van {$content->stage4->createdAt|date:"d-m-Y"} heb ik u een subsidie verleend van
        {$content->stage2->amount} voor de regeling {$content->subsidyTitle}. Uit de aan mij ter beschikking
        gestelde gegevens blijkt dat u inmiddels voldoet aan de vereisten voor deze regeling.
    </p>
    <p>&nbsp;</p>

    <h2>Besluit</h2>
    <p>Ik stel hierbij de subsidie vast op {$content->stage4->amount}.</p>
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
', e'{layout \'letter_layout.latte\'}

{block concern}
    Betreft: Vaststelling subsidie {$content->subsidyTitle}
{/block}

{block content}
    <p>Beste lezer,</p>
    <p>Met mijn brief van {$content->stage4->createdAt|date:"d-m-Y"} heb ik u een subsidie verleend van
        {$content->stage2->amount} voor de regeling {$content->subsidyTitle}. Uit de aan mij ter beschikking
        gestelde gegevens blijkt dat u inmiddels voldoet aan de vereisten voor deze regeling.
    </p>
    <p>&nbsp;</p>

    <h2>Besluit</h2>
    <p>Ik stel hierbij de subsidie vast op {$content->stage4->amount}.</p>
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

    <h4>Verplichtingennummer</h4>
    <p>{$content->stage2->liabilitiesNumber}</p>
{/block}
', '2024-03-25 23:19:33', null);
INSERT INTO public.subsidy_stage_transition_messages (id, subsidy_stage_transition_id, version, status, subject, content_html, content_pdf, created_at, updated_at) VALUES ('d9917011-3baf-4a5f-8b1f-0e8e2b62d0a4', '3ad4bbc8-4810-4d57-a004-f059a930df17', 1, 'published', 'Vordering aanvraag', e'{block content}
    <p>Beste lezer,</p>
    <p>Met mijn brief van {$content->stage4->createdAt|date:"d-m-Y"} heb ik u een subsidie verleend van
        {$content->stage2->amount} voor de regeling {$content->subsidyTitle}. Uit de aan mij ter beschikking
        gestelde gegevens blijkt dat u inmiddels voldoet aan de vereisten voor deze regeling.
    </p>
    <p>&nbsp;</p>

    <h2>Besluit</h2>
    <p>Ik stel hierbij de subsidie vast op {$content->stage4->amount}.</p>
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
', e'{layout \'letter_layout.latte\'}

{block concern}
    Betreft: Vordering subsidie {$content->subsidyTitle}
{/block}

{block content}
    <p>Beste lezer,</p>
    <p>Met mijn brief van {$content->stage4->createdAt|date:"d-m-Y"} heb ik u een subsidie verleend van
        {$content->stage2->amount} voor de regeling {$content->subsidyTitle}. Uit de aan mij ter beschikking
        gestelde gegevens blijkt dat u inmiddels voldoet aan de vereisten voor deze regeling.
    </p>
    <p>&nbsp;</p>

    <h2>Besluit</h2>
    <p>Ik stel hierbij de subsidie vast op {$content->stage4->amount}.</p>
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

    <h4>Verplichtingennummer</h4>
    <p>{$content->stage2->liabilitiesNumber}</p>
{/block}
', '2024-03-25 23:19:33', null);
