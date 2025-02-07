INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id, required_condition, retention_period_on_approval, exclude_from_clone_data) VALUES ('6d07c38d-1b8f-4686-8a10-3df080edd56b', 'Aanvullende informatie behandelaar', null, 'upload', '{"maxItems": 20, "minItems": 1, "mimeTypes": ["image/jpeg", "image/png", "application/pdf"], "maxFileSize": 20971520}', false, 'firstAssessmentAdditionalDocuments', 'user', '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c', null, 'short', false);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id, required_condition, retention_period_on_approval, exclude_from_clone_data) VALUES ('8565d370-3230-406d-aa95-fe3894ebc820', 'Aanvullende informatie behandelaar', null, 'upload', '{"maxItems": 20, "minItems": 1, "mimeTypes": ["image/jpeg", "image/png", "application/pdf"], "maxFileSize": 20971520}', false, 'secondAssessmentAdditionalDocuments', 'user', 'b2b08566-8493-4560-8afa-d56402931f74', null, 'short', false);

UPDATE public.subsidy_stage_uis
SET
    updated_at = 'now()',
    input_ui = '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Beoordeling","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentChecklist","options":{"format":"checkbox-group"}}]}]},{"type":"Group","label":"Financi\u00eble afhandeling","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/businessPartnerNumber","label":"Zakenpartnernummer","options":{"placeholder":""}}]}]},{"type":"Group","label":"Uitkering","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/amount","options":{"format":"radio"}}]}]},{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessment","options":{"format":"radio"}}]}]},{"type":"Group","label":"Toelichting","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRequestedComplementReason","options":{"format":"radio"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRequestedComplementNote","options":{"format":"textarea","tip":"Deze notitie wordt opgenomen binnen de brief aan de aanvrager."},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRejectedNote","options":{"format":"textarea","tip":"Deze notitie wordt opgenomen binnen de brief aan de aanvrager."},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Afgekeurd"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentApprovedNote","options":{"format":"textarea","tip":"Deze notitie wordt opgenomen binnen de brief aan de aanvrager."},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Goedgekeurd"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentAdditionalDocuments","options":{"accept":"image\/jpeg,image\/png,application\/pdf","maxFileSize":20971520,"minItems":1,"maxItems":20,"tip":"Upload een of meerdere bewijsstukken. Toegestane bestandstypen: pdf, jpg, jpeg, png. Maximale bestandsgrootte: 20 MB."}},{"type":"CustomControl","scope":"#\/properties\/firstAssessmentInternalNote","options":{"format":"textarea"}}]}]}]}',
    view_ui = '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"FormGroupControl","label":"Beoordeling","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentChecklist","options":{"readonly":true,"format":"checkbox-group"}}]},{"type":"FormGroupControl","label":"Financi\u00eble afhandeling","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/businessPartnerNumber","label":"Zakenpartnernummer","options":{"readonly":true}}]},{"type":"FormGroupControl","label":"Uitkering","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/amount","options":{"readonly":true}}]},{"type":"FormGroupControl","label":"Eerste beoordeling","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRequestedComplementReason","options":{"readonly":true,"format":"radio"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}},{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRequestedComplementNote","options":{"readonly":true,"format":"textarea"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}},{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRejectedNote","options":{"readonly":true,"format":"textarea"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Afgekeurd"}}}},{"type":"CustomControl","scope":"#\/properties\/firstAssessmentApprovedNote","options":{"readonly":true,"format":"textarea"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Goedgekeurd"}}}},{"type":"FormResultsTable","options":{"fields":{"Aanvullende informatie behandelaar":"{firstAssessmentAdditionalDocuments}","Interne notitie":"{firstAssessmentInternalNote}"},"showEmptyFields":true}}]}]}'
WHERE id = 'db1076a1-42f3-4c90-b1bf-57d1db025f2e';

UPDATE public.subsidy_stage_uis
SET
    updated_at = 'now()',
    input_ui = '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessorMotivatedValid","options":{"format":"checkbox"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/secondAssessment","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/secondAssessmentAdditionalDocuments","options":{"accept":"image\/jpeg,image\/png,application\/pdf","maxFileSize":20971520,"minItems":1,"maxItems":20,"tip":"Upload een of meerdere bewijsstukken. Toegestane bestandstypen: pdf, jpg, jpeg, png. Maximale bestandsgrootte: 20 MB."}},{"type":"CustomControl","scope":"#\/properties\/secondAssessmentInternalNote","options":{"format":"textarea"}}]}]}]}',
    view_ui = '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessorMotivatedValid","options":{"readonly":true}},{"type":"CustomControl","scope":"#\/properties\/secondAssessment","options":{"readonly":true,"format":"radio"}},{"type":"FormResultsTable","options":{"fields":{"Aanvullende informatie behandelaar":"{secondAssessmentAdditionalDocuments}","Interne notitie":"{secondAssessmentInternalNote}"},"showEmptyFields":true}}]}'
WHERE id = '787c8ef4-bfcd-4bd4-aec1-dec02139c897';
