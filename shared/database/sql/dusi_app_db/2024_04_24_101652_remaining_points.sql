UPDATE public.fields
SET params = '{
    "default": null,
    "options": [
        "Eens met de eerste beoordeling",
        "Oneens met de eerste beoordeling"
    ]
}'
WHERE code = 'secondAssessment'
  AND subsidy_stage_id = 'b2b08566-8493-4560-8afa-d56402931f74';

UPDATE public.subsidy_stage_transitions
SET assign_to_previous_assessor = true
WHERE id = 'cd0491f3-9eef-4094-87fa-ae3babcacd04';

-- AIGT assessment field changes

INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition, retention_period_on_approval, exclude_from_clone_data)
VALUES ('8b606c86-0cca-45e4-a9a1-21c9bf52b665', 'Controlevragen', null, 'multiselect', '{
    "options": [
        "Akkoord met de vaststelling",
        "De verplichting is geaccordeerd in SAP",
        "De vaststellingsbrief mag verzonden worden"
    ]
}', false, 'assignationImplementationAssessmentChecklist', 'user', '051364be-fa12-4af7-a1b8-c80f5e9dd652', null,
        'short', false);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition, retention_period_on_approval, exclude_from_clone_data)
VALUES ('1ed7c7ef-6a37-407c-a931-d4afb08ce115', 'Controlevragen', null, 'multiselect', '{
    "options": [
        "OIGT heeft de afronding van de buitenlandstage bevestigd",
        "De verplichting is vastgesteld"
    ]
}', false, 'assignationAssessmentChecklist', 'user', '59ddbc42-8ffc-4e2c-a751-d937714b6df6', null, 'short', false);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition, retention_period_on_approval, exclude_from_clone_data)
VALUES ('b391e3e3-0cf5-4956-ae78-3ab0435f3874', 'Controlevragen', null, 'multiselect', '{
    "options": [
        "OIGT heeft de afronding van de buitenlandstage bevestigd",
        "De verplichting is vastgesteld"
    ]
}', false, 'assignationAssessmentChecklist', 'user', '2b06aee1-ea36-41a4-b7ae-74fa53c64a64', null, 'short', false);


INSERT INTO public.subsidy_stage_uis (id, subsidy_stage_id, version, status, input_ui, created_at, updated_at, view_ui)
VALUES ('6b9e3359-8c44-4bfd-a593-baa5c4b8d19d', '59ddbc42-8ffc-4e2c-a751-d937714b6df6', 1, 'published', '{
    "type": "FormGroupControl",
    "options": {
        "section": true,
        "group": true
    },
    "elements": [
        {
            "type": "Group",
            "label": "Beoordeling",
            "elements": [
                {
                    "type": "VerticalLayout",
                    "elements": [
                        {
                            "type": "CustomControl",
                            "scope": "#\/properties\/assignationAssessmentChecklist",
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
                            "scope": "#\/properties\/assessment",
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
                            "scope": "#\/properties\/proof",
                            "label": "Bewijs",
                            "options": {
                                "accept": "image\/jpeg,image\/png,application\/pdf",
                                "maxFileSize": 20971520,
                                "minItems": 1,
                                "maxItems": 20,
                                "tip": "Upload een of meerdere bewijsstukken. Toegestane bestandstypen: pdf, jpg, jpeg, png. Maximale bestandsgrootte: 20 MB."
                            }
                        },
                        {
                            "type": "CustomControl",
                            "scope": "#\/properties\/internalNote",
                            "options": {
                                "format": "textarea"
                            }
                        }
                    ]
                }
            ]
        }
    ]
}', null, null, '{
    "type": "FormGroupControl",
    "options": {
        "section": true
    },
    "elements": [
        {
            "type": "CustomControl",
            "scope": "#\/properties\/assignationAssessmentChecklist",
            "options": {
                "readonly": true,
                "format": "checkbox-group"
            }
        },
        {
            "type": "CustomControl",
            "scope": "#\/properties\/assessment",
            "options": {
                "readonly": true,
                "format": "radio"
            }
        },
        {
            "type": "FormResultsTable",
            "options": {
                "fields": {
                    "Bewijs": "{proof}"
                }
            }
        },
        {
            "type": "CustomControl",
            "scope": "#\/properties\/internalNote",
            "options": {
                "readonly": true,
                "format": "textarea"
            }
        }
    ]
}');
INSERT INTO public.subsidy_stage_uis (id, subsidy_stage_id, version, status, input_ui, created_at, updated_at, view_ui)
VALUES ('2a227775-700d-4f59-9322-900bb326afff', '2b06aee1-ea36-41a4-b7ae-74fa53c64a64', 1, 'published', '{
    "type": "FormGroupControl",
    "options": {
        "section": true,
        "group": true
    },
    "elements": [
        {
            "type": "Group",
            "label": "Beoordeling",
            "elements": [
                {
                    "type": "VerticalLayout",
                    "elements": [
                        {
                            "type": "CustomControl",
                            "scope": "#\/properties\/assignationAssessmentChecklist",
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
                            "scope": "#\/properties\/assessment",
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
                            "scope": "#\/properties\/proof",
                            "label": "Bewijs",
                            "options": {
                                "accept": "image\/jpeg,image\/png,application\/pdf",
                                "maxFileSize": 20971520,
                                "minItems": 1,
                                "maxItems": 20,
                                "tip": "Upload een of meerdere bewijsstukken. Toegestane bestandstypen: pdf, jpg, jpeg, png. Maximale bestandsgrootte: 20 MB."
                            }
                        },
                        {
                            "type": "CustomControl",
                            "scope": "#\/properties\/internalNote",
                            "options": {
                                "format": "textarea"
                            }
                        }
                    ]
                }
            ]
        }
    ]
}', null, null, '{
    "type": "FormGroupControl",
    "options": {
        "section": true
    },
    "elements": [
        {
            "type": "CustomControl",
            "scope": "#\/properties\/assignationAssessmentChecklist",
            "options": {
                "readonly": true,
                "format": "checkbox-group"
            }
        },
        {
            "type": "CustomControl",
            "scope": "#\/properties\/assessment",
            "options": {
                "readonly": true,
                "format": "radio"
            }
        },
        {
            "type": "FormResultsTable",
            "options": {
                "fields": {
                    "Bewijs": "{proof}"
                }
            }
        },
        {
            "type": "CustomControl",
            "scope": "#\/properties\/internalNote",
            "options": {
                "readonly": true,
                "format": "textarea"
            }
        }
    ]
}');
INSERT INTO public.subsidy_stage_uis (id, subsidy_stage_id, version, status, input_ui, created_at, updated_at, view_ui)
VALUES ('9fb35125-318e-4426-8857-facefdd94fee', '051364be-fa12-4af7-a1b8-c80f5e9dd652', 1, 'published', '{
    "type": "FormGroupControl",
    "options": {
        "section": true,
        "group": true
    },
    "elements": [
        {
            "type": "Group",
            "label": "Beoordeling",
            "elements": [
                {
                    "type": "VerticalLayout",
                    "elements": [
                        {
                            "type": "CustomControl",
                            "scope": "#\/properties\/assignationImplementationAssessmentChecklist",
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
                            "scope": "#\/properties\/assessment",
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
                            "scope": "#\/properties\/internalNote",
                            "options": {
                                "format": "textarea"
                            }
                        }
                    ]
                }
            ]
        }
    ]
}', null, null, '{
    "type": "FormGroupControl",
    "options": {
        "section": true
    },
    "elements": [
        {
            "type": "CustomControl",
            "scope": "#\/properties\/assignationImplementationAssessmentChecklist",
            "options": {
                "readonly": true,
                "format": "checkbox-group"
            }
        },
        {
            "type": "CustomControl",
            "scope": "#\/properties\/assessment",
            "options": {
                "readonly": true,
                "format": "radio"
            }
        },
        {
            "type": "CustomControl",
            "scope": "#\/properties\/internalNote",
            "options": {
                "readonly": true,
                "format": "textarea"
            }
        }
    ]
}');

-- END AIGT assessment field changes
