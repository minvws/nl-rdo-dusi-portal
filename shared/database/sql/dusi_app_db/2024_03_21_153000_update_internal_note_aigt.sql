UPDATE public.subsidy_stage_uis
SET
input_ui = '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/implementationCoordinatorAssessment","options":{"format":"radio"}}]}]},{"type":"Group","label":"Toelichting","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/internalNote","options":{"format":"textarea"}}]}]}]}',
view_ui = '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"CustomControl","scope":"#/properties/implementationCoordinatorAssessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#/properties/internalNote","options":{"readonly":true,"format":"textarea"}}]}'
WHERE id = '6a669ec1-e949-40d8-bbc4-946665553fb1';
