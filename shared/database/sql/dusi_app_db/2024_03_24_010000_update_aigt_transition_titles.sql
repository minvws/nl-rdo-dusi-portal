UPDATE public.subsidy_stage_transitions
    SET description = 'Uitvoeringscoördinator is het eens met de afkeuring'
    WHERE id = '3063cb42-5d55-4b9b-82e6-6250a4481296';

UPDATE public.subsidy_stage_transitions
    SET description = 'Uitvoeringscoördinator is het eens met de goedkeuring'
    WHERE id = '9fc7740b-1951-4e99-8f89-5608bb0e3a0b';

UPDATE public.subsidy_stage_transitions
    SET description = 'Uitvoeringscoördinator is het eens met de vordering'
    WHERE id = '66d64304-b165-4ada-9cf0-eb28b2772e47';

UPDATE public.subsidy_stage_transitions
    SET description = 'Uitvoeringscoördinator is het eens met de vaststellingsbeoordeling'
    WHERE id = '6a4d09fe-c648-45d3-b404-beaa95cb1013';

UPDATE public.subsidy_stage_transitions
    SET description = 'Uitvoeringscoördinator is het oneens met de vaststellingsbeoordeling'
    WHERE id = 'b4387d82-1c34-4434-9e8b-aa4a5048f8d4';
