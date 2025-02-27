-- Update AIGT stage 3
UPDATE public.subsidy_stage_uis
SET input_ui = '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Checklist","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#/properties/internalAssessmentChecklist","options":{"format":"checkbox-group"}}]}]},{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#/properties/firstAssessorMotivatedValid","options":{"format":"checkbox"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#/properties/internalAssessment","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#/properties/internalAssessmentInternalNote","options":{"format":"textarea"}}]}]}]}',
    view_ui = '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"CustomControl","scope":"#/properties/internalAssessmentChecklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#/properties/firstAssessorMotivatedValid","options":{"readonly":true}},{"type":"CustomControl","scope":"#/properties/internalAssessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#/properties/internalAssessmentInternalNote","options":{"readonly":true,"format":"textarea"}}]}'
where id = '8f7b2a5f-050e-4dd2-9d05-4e1d20f3929a';

-- Update AIGT stage 4
UPDATE public.subsidy_stage_uis
SET input_ui = '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#/properties/implementationCoordinatorAssessment","options":{"format":"radio"}}]}]},{"type":"Group","label":"Toelichting","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#/properties/internalNote","options":{"format":"textarea"}}]}]}]}',
    view_ui = '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"CustomControl","scope":"#/properties/implementationCoordinatorAssessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#/properties/internalNote","options":{"readonly":true,"format":"textarea"}}]}'
where id = '6a669ec1-e949-40d8-bbc4-946665553fb1';

-- Update AIGT stage 5
UPDATE public.subsidy_stage_uis
SET input_ui = '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Beoordeling","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#/properties/assignationAssessmentChecklist","options":{"format":"checkbox-group"}}]}]},{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#/properties/assessment","options":{"format":"radio"}}]}]},{"type":"Group","label":"Toelichting","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#/properties/proof","label":"Bewijs","options":{"accept":"image/jpeg,image/png,application/pdf","maxFileSize":20971520,"minItems":1,"maxItems":20,"tip":"Upload een of meerdere bewijsstukken. Toegestane bestandstypen: pdf, jpg, jpeg, png. Maximale bestandsgrootte: 20 MB."}},{"type":"CustomControl","scope":"#/properties/internalNote","options":{"format":"textarea"}}]}]}]}',
    view_ui = '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"CustomControl","scope":"#/properties/assignationAssessmentChecklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#/properties/assessment","options":{"readonly":true,"format":"radio"}},{"type":"FormResultsTable","options":{"fields":{"Bewijs":"{proof}","Interne notitie":"{internalNote}"},"showEmptyFields":true}}]}'
where id = '6b9e3359-8c44-4bfd-a593-baa5c4b8d19d';

-- Update AIGT stage 6
UPDATE public.subsidy_stage_uis
SET input_ui = '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Beoordeling","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#/properties/assignationAssessmentChecklist","options":{"format":"checkbox-group"}}]}]},{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#/properties/assessment","options":{"format":"radio"}}]}]},{"type":"Group","label":"Toelichting","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#/properties/proof","label":"Bewijs","options":{"accept":"image/jpeg,image/png,application/pdf","maxFileSize":20971520,"minItems":1,"maxItems":20,"tip":"Upload een of meerdere bewijsstukken. Toegestane bestandstypen: pdf, jpg, jpeg, png. Maximale bestandsgrootte: 20 MB."}},{"type":"CustomControl","scope":"#/properties/internalNote","options":{"format":"textarea"}}]}]}]}',
    view_ui = '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"CustomControl","scope":"#/properties/assignationAssessmentChecklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#/properties/assessment","options":{"readonly":true,"format":"radio"}},{"type":"FormResultsTable","options":{"fields":{"Bewijs":"{proof}","Interne notitie":"{internalNote}"},"showEmptyFields":true}}]}'
where id = '2a227775-700d-4f59-9322-900bb326afff';

-- Update AIGT stage 7
UPDATE public.subsidy_stage_uis
SET input_ui = '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Beoordeling","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#/properties/assignationImplementationAssessmentChecklist","options":{"format":"checkbox-group"}}]}]},{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#/properties/assessment","options":{"format":"radio"}}]}]},{"type":"Group","label":"Toelichting","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#/properties/internalNote","options":{"format":"textarea"}}]}]}]}',
    view_ui = '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"CustomControl","scope":"#/properties/assignationImplementationAssessmentChecklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#/properties/assessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#/properties/internalNote","options":{"readonly":true,"format":"textarea"}}]}'
where id = '9b0a617b-25dd-474c-bc0a-912c503a35e8';

--Move field from stage 8 to stage 7
UPDATE public.fields
SET subsidy_stage_id = 'dfd0310d-3bf6-4e38-a2d5-0a3223ac20c8'
WHERE code = 'assignationImplementationAssessmentChecklist'
  AND subsidy_stage_id = '051364be-fa12-4af7-a1b8-c80f5e9dd652';

-- Update stage 8
UPDATE public.subsidy_stage_uis
SET input_ui = '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#/properties/assessment","options":{"format":"radio"}}]}]},{"type":"Group","label":"Toelichting","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#/properties/internalNote","options":{"format":"textarea"}}]}]}]}',
    view_ui = '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"CustomControl","scope":"#/properties/assessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#/properties/internalNote","options":{"readonly":true,"format":"textarea"}}]}'
where id = '9fb35125-318e-4426-8857-facefdd94fee';
