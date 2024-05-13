UPDATE public.subsidy_stage_uis
SET view_ui = '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"CustomControl","scope":"#\/properties\/assessment","options":{"readonly":true,"format":"radio"}},{"type":"FormResultsTable","options":{"fields":{"Bewijs":"{proof}","Interne notitie":"{internalNote}"},"showEmptyFields":true}}]}'
WHERE id = 'ef196de1-5c15-4af3-9ec8-046ca4419fd1';

UPDATE public.subsidy_stage_uis
SET view_ui = '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"CustomControl","scope":"#\/properties\/assessment","options":{"readonly":true,"format":"radio"}},{"type":"FormResultsTable","options":{"fields":{"Bewijs":"{proof}","Interne notitie":"{internalNote}"},"showEmptyFields":true}}]}'
WHERE id = 'd15ff747-b912-4abc-b6df-2a750c820d92';
