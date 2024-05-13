UPDATE public.subsidy_stage_uis
SET view_ui = '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"CustomControl","scope":"#\/properties\/assignationAssessmentChecklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#\/properties\/assessment","options":{"readonly":true,"format":"radio"}},{"type":"FormResultsTable","options":{"fields":{"Bewijs":"{proof}","Interne notitie":"{internalNote}"},"showEmptyFields":true}}]}'
WHERE id = '6b9e3359-8c44-4bfd-a593-baa5c4b8d19d';

UPDATE public.subsidy_stage_uis
SET view_ui = '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"CustomControl","scope":"#\/properties\/assignationAssessmentChecklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#\/properties\/assessment","options":{"readonly":true,"format":"radio"}},{"type":"FormResultsTable","options":{"fields":{"Bewijs":"{proof}","Interne notitie":"{internalNote}"},"showEmptyFields":true}}]}'
WHERE id = '2a227775-700d-4f59-9322-900bb326afff';
