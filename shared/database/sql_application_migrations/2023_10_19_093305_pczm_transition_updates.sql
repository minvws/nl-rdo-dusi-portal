-- https://github.com/minvws/nl-rdo-dusi-portal/issues/547

-- Wanneer de aanvraag wordt aangevuld door de aanvrager
UPDATE public.subsidy_stage_transitions
SET clone_data = true
WHERE id = '7ac879d1-63cb-478d-8745-737313f1643e';

-- Wanneer de aanvraag wordt teruggezet in het behandelproces bij oneens door 2e behandelaar
UPDATE public.subsidy_stage_transitions
SET clone_data = true
WHERE id = 'c33b8459-3a98-4906-9ce0-c6f9c0ae7a49';

-- Wanneer de aanvraag wordt teruggezet in het behandelproces door mismatch 1e behandelaar en IC
UPDATE public.subsidy_stage_transitions
SET clone_data = true
WHERE id = '005a5acb-a908-44d2-8b69-a50d5ef43870';
