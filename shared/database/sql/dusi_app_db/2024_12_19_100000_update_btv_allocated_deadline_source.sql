UPDATE public.subsidy_stage_transitions
SET target_application_review_deadline_source = 'now',
    target_application_review_deadline_additional_period = 'P1Y'
WHERE id = '16f83400-7ff9-41ce-8ad7-040e316b8cee';
