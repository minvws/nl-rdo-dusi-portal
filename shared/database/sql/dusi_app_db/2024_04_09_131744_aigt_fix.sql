INSERT INTO public.fields (id, title, description, type,
                           params, is_required, code, source, subsidy_stage_id,
                           required_condition, retention_period_on_approval, exclude_from_clone_data)
VALUES ('105120eb-fe79-4e50-b5a0-c94ddef4ab94', 'Motivatie van goedkeuring', null, 'text',
        '{
            "maxLength": null
        }', false, 'firstAssessmentApprovedNote', 'user', '7075fcad-7d92-42f6-b46c-7733869019e0',
        null, 'short', false);

UPDATE public.fields
SET code = 'internalNote'
WHERE code = 'implementationCoordinatorAssessmentInternalNote' AND subsidy_stage_id = 'e5da8f2e-db87-45df-8967-ea3dceb2b207';
