UPDATE public.subsidy_stage_uis
SET view_ui = '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"CustomControl","scope":"#\/properties\/implementationCoordinatorAssessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/coordinatorImplementationInternalNote","options":{"readonly":true,"format":"textarea"}}]}'
WHERE id = 'c51302f6-e131-45ff-8d4b-f4ff4a39b52f';
