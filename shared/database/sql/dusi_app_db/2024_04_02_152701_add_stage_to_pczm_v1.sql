INSERT INTO public.subsidy_stages (id, created_at, subsidy_version_id, title, subject_role, subject_organisation, stage,
                                   assessor_user_role, internal_note_field_code, allow_duplicate_assessors)
VALUES ('ef2238cf-a8ce-4376-ab2e-e821bc43ddb5', null, '513011cd-789b-4628-ba5c-2fee231f8959',
        'Informeren over verhoging van toegekend bedrag', 'assessor', null, 6, 'implementationCoordinator', null,
        false);

INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition, retention_period_on_approval, exclude_from_clone_data)
VALUES ('2c9e3924-b916-42e5-89d0-7e806fef1d26', 'Interne notitie', null, 'text', 'null', false, 'increaseAmountInternalNote',
        'user', 'ef2238cf-a8ce-4376-ab2e-e821bc43ddb5', null, 'short', false);


INSERT INTO public.subsidy_stage_transitions (id, current_subsidy_stage_id, target_subsidy_stage_id,
                                              target_application_status, condition, send_message, clone_data,
                                              assign_to_previous_assessor, description, expiration_period,
                                              evaluation_trigger)
VALUES ('2b493130-c191-4455-8de4-d932ab6c2b60', 'ef2238cf-a8ce-4376-ab2e-e821bc43ddb5', null, null, '{
    "type": "and",
    "conditions": [
        {
            "type": "comparison",
            "stage": 2,
            "fieldCode": "firstAssessment",
            "operator": "===",
            "value": "Goedgekeurd"
        },
        {
            "type": "comparison",
            "stage": 5,
            "fieldCode": "implementationCoordinatorAssessment",
            "operator": "===",
            "value": "Goedgekeurd"
        }
    ]
}', true, false, false, 'Toegekend bedrag verhoogd', null, 'submit');
