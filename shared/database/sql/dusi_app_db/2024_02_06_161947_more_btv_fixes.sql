UPDATE public.fields
SET is_required = false
WHERE code = 'firstAssessmentChecklist'
  AND subsidy_stage_id = '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c';

update public.subsidy_stage_transitions
set clone_data = true
where id = '3a293e03-1de4-47bf-917b-841b7c0a1fff';

UPDATE public.subsidy_stage_uis
SET input_ui         = '{
        "type": "FormGroupControl",
        "options": {
            "section": true,
            "group": true
        },
        "elements": [
            {
                "type": "Group",
                "label": "Checklist",
                "elements": [
                    {
                        "type": "VerticalLayout",
                        "elements": [
                            {
                                "type": "CustomControl",
                                "scope": "#\/properties\/internalAssessmentChecklist",
                                "options": {
                                    "format": "checkbox-group"
                                }
                            }
                        ]
                    }
                ]
            },
            {
                "type": "Group",
                "label": "Status",
                "elements": [
                    {
                        "type": "VerticalLayout",
                        "elements": [
                            {
                                "type": "CustomControl",
                                "scope": "#\/properties\/internalAssessment",
                                "options": {
                                    "format": "radio"
                                }
                            }
                        ]
                    }
                ]
            },
            {
                "type": "Group",
                "label": "Toelichting",
                "elements": [
                    {
                        "type": "VerticalLayout",
                        "elements": [
                            {
                                "type": "CustomControl",
                                "scope": "#\/properties\/internalAssessmentInternalNote",
                                "options": {
                                    "format": "textarea"
                                }
                            }
                        ]
                    }
                ]
            }
        ]
    }',
    updated_at       = now(),
    view_ui          = '{
        "type": "FormGroupControl",
        "options": {
            "section": true
        },
        "elements": [
            {
                "type": "CustomControl",
                "scope": "#\/properties\/internalAssessmentChecklist",
                "options": {
                    "readonly": true,
                    "format": "checkbox-group"
                }
            },
            {
                "type": "CustomControl",
                "scope": "#\/properties\/internalAssessment",
                "options": {
                    "readonly": true,
                    "format": "radio"
                }
            },
            {
                "type": "CustomControl",
                "scope": "#\/properties\/internalAssessmentInternalNote",
                "options": {
                    "readonly": true,
                    "format": "textarea"
                }
            }
        ]
    }'
WHERE id = 'a6080627-0ea9-436e-bbba-c454bd3809fd';
