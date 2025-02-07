UPDATE public.subsidy_stages
    SET internal_note_field_code = 'internalAssessmentInternalNote'
    WHERE id = '0838f8a9-b2ff-4669-9d42-1c51a1134a34';

UPDATE public.subsidy_stage_uis
    SET view_ui = '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessorMotivatedValid","options":{"readonly":true}},{"type":"CustomControl","scope":"#\/properties\/internalAssessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/internalAssessmentInternalNote","options":{"readonly":true,"format":"textarea"}}]}'
    WHERE id = '8f7b2a5f-050e-4dd2-9d05-4e1d20f3929a';
