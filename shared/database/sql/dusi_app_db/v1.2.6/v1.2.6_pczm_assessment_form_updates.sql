-- # https://github.com/minvws/nl-rdo-dusi-portal/issues/504

-- First delete data from answers table, due to constraint. No data present on prod yet.
DELETE FROM answers WHERE field_id IN (select id FROM "fields"
WHERE "subsidy_stage_id" = '8027c102-93ef-4735-ab66-97aa63b836eb' AND
      "code" = 'firstSickDayWithinExpiryDate');

DELETE
FROM "fields"
WHERE "subsidy_stage_id" = '8027c102-93ef-4735-ab66-97aa63b836eb' AND
      "code" = 'firstSickDayWithinExpiryDate';

UPDATE public.fields
SET title              = 'Controlevragen',
    description        = null,
    type               = 'multiselect',
    params             = '{"options":["Alle aangeleverde documenten zijn te herleiden tot dezelfde persoon op basis van BSN en de overige persoonsgegevens","Het IBAN bestaat en is actief","Het opgegeven IBAN staat op naam van de aanvrager of bewindvoerder","Op basis van de SurePay terugkoppeling, en de controle of de aanvrager onder bewind staat, ben ik akkoord met het opgegeven rekeningnummer"]}',
    is_required        = false,
    code               = 'personalDataChecklist',
    source             = 'user',
    subsidy_stage_id   = '8027c102-93ef-4735-ab66-97aa63b836eb',
    required_condition = null
WHERE "subsidy_stage_id" = '8027c102-93ef-4735-ab66-97aa63b836eb' AND
      "code" = 'personalDataChecklist';

UPDATE public.fields
SET title              = 'Controlevragen',
    description        = null,
    type               = 'multiselect',
    params             = '{"options":["Op basis van het medisch onderzoeksverslag (medische rapportage) en\/of de verklaring van de arts is vast te stellen dat er een post-COVID diagnose is gesteld","De post-COVID diagnose is v\u00f3\u00f3r 1 juni 2023 gesteld"]}',
    is_required        = false,
    code               = 'postCovidChecklist',
    source             = 'user',
    subsidy_stage_id   = '8027c102-93ef-4735-ab66-97aa63b836eb',
    required_condition = null
WHERE "subsidy_stage_id" = '8027c102-93ef-4735-ab66-97aa63b836eb' AND
      "code" = 'postCovidChecklist';

UPDATE public.subsidy_stage_uis
SET subsidy_stage_id = '8027c102-93ef-4735-ab66-97aa63b836eb',
    version          = 1,
    status           = 'published',
    input_ui         = '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Persoonsgegevens","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/personalDataChecklist","options":{"format":"checkbox-group"}}]}]},{"type":"Group","label":"Vaststellen WIA","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/wiaChecklist","options":{"format":"checkbox-group"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/WIADecisionIndicates","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/IVA_Or_WIA_Checklist","options":{"format":"checkbox-group"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/WIA_RejectedOnHighSalaryChecklist","options":{"format":"checkbox-group"}}]}]},{"type":"Group","label":"Zorgaanbieder en functie","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/employerChecklist","options":{"format":"checkbox-group"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/healthcareProviderStatementIsComplete","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/employerName","options":[]}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/healthcareProviderName","options":[]}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/chamberOfCommerceNumberHealthcareProvider","options":[]}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/healthcareProviderChecklist","options":{"format":"checkbox-group"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/healthcareProviderSBICode","options":[]}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/healthcareProviderAGBCode","options":[]}]}]},{"type":"Group","label":"Justiti\u00eble inrichting","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/judicialInstitutionIsEligible","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/applicantFoundInBigRegister","options":{"format":"radio"}}]}]},{"type":"Group","label":"Vaststellen post-COVID","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/postCovidChecklist","options":{"format":"checkbox-group"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/doctorFoundInBigRegister","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/doctorsCertificateIsComplete","options":{"format":"radio"}}]}]},{"type":"Group","label":"Uitkering","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/amount","options":{"format":"radio"}}]}]},{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessment","options":{"format":"radio"}}]}]},{"type":"Group","label":"Toelichting","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRequestedComplementReason","options":{"format":"radio"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRequestedComplementNote","options":{"format":"textarea","tip":"Deze notitie wordt opgenomen binnen de brief aan de aanvrager."},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRejectedNote","options":{"format":"textarea","tip":"Deze notitie wordt opgenomen binnen de brief aan de aanvrager."},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Afgekeurd"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentInternalNote","options":{"format":"textarea"}}]}]}]}',
    created_at       = null,
    updated_at       = null,
    view_ui          = '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"FormGroupControl","label":"Persoonsgegevens","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/personalDataChecklist","options":{"readonly":true,"format":"checkbox-group"}}]},{"type":"FormGroupControl","label":"Vaststellen WIA","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/wiaChecklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#\/properties\/WIADecisionIndicates","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/IVA_Or_WIA_Checklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#\/properties\/WIA_RejectedOnHighSalaryChecklist","options":{"readonly":true,"format":"checkbox-group"}}]},{"type":"FormGroupControl","label":"Zorgaanbieder en functie","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/employerChecklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#\/properties\/healthcareProviderStatementIsComplete","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/employerName","options":{"readonly":true}},{"type":"CustomControl","scope":"#\/properties\/healthcareProviderName","options":{"readonly":true}},{"type":"CustomControl","scope":"#\/properties\/chamberOfCommerceNumberHealthcareProvider","options":{"readonly":true}},{"type":"CustomControl","scope":"#\/properties\/healthcareProviderChecklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#\/properties\/healthcareProviderSBICode","options":{"readonly":true}},{"type":"CustomControl","scope":"#\/properties\/healthcareProviderAGBCode","options":{"readonly":true}}]},{"type":"FormGroupControl","label":"Justiti\u00eble inrichting","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/judicialInstitutionIsEligible","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/applicantFoundInBigRegister","options":{"readonly":true,"format":"radio"}}]},{"type":"FormGroupControl","label":"Vaststellen post-COVID","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/postCovidChecklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#\/properties\/doctorFoundInBigRegister","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/doctorsCertificateIsComplete","options":{"readonly":true,"format":"radio"}}]},{"type":"FormGroupControl","label":"Eerste beoordeling","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/amount","options":{"readonly":true}},{"type":"CustomControl","scope":"#\/properties\/firstAssessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRequestedComplementReason","options":{"readonly":true,"format":"radio"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}},{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRequestedComplementNote","options":{"readonly":true,"format":"textarea"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}},{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRejectedNote","options":{"readonly":true,"format":"textarea"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Afgekeurd"}}}},{"type":"CustomControl","scope":"#\/properties\/firstAssessmentInternalNote","options":{"readonly":true,"format":"textarea"}}]}]}'
WHERE id = '71f71916-c0ed-45bc-8186-1b4f5dfb69e8';


-- # https://github.com/minvws/nl-rdo-dusi-portal/issues/471

UPDATE public.fields
SET title              = 'Toelichting van benodigde aanvullingen',
    description        = null,
    type               = 'text',
    params             = '{"maxLength":null}',
    is_required        = false,
    code               = 'firstAssessmentRequestedComplementNote',
    source             = 'user',
    subsidy_stage_id   = '8027c102-93ef-4735-ab66-97aa63b836eb',
    required_condition = null
WHERE "subsidy_stage_id" = '8027c102-93ef-4735-ab66-97aa63b836eb' AND
      "code" = 'firstAssessmentRequestedComplementNote';

UPDATE public.fields
SET title              = 'Reden van afkeuring',
    description        = null,
    type               = 'text',
    params             = '{"maxLength":null}',
    is_required        = false,
    code               = 'firstAssessmentRejectedNote',
    source             = 'user',
    subsidy_stage_id   = '8027c102-93ef-4735-ab66-97aa63b836eb',
    required_condition = null
WHERE "subsidy_stage_id" = '8027c102-93ef-4735-ab66-97aa63b836eb' AND
      "code" = 'firstAssessmentRejectedNote';

UPDATE public.fields
SET title              = 'Interne notitie',
    description        = null,
    type               = 'text',
    params             = '{"maxLength":null}',
    is_required        = false,
    code               = 'firstAssessmentInternalNote',
    source             = 'user',
    subsidy_stage_id   = '8027c102-93ef-4735-ab66-97aa63b836eb',
    required_condition = null
WHERE "subsidy_stage_id" = '8027c102-93ef-4735-ab66-97aa63b836eb' AND
      "code" = 'firstAssessmentInternalNote';

UPDATE public.fields
SET title              = 'Interne notitie',
    description        = null,
    type               = 'text',
    params             = '{"maxLength":null}',
    is_required        = false,
    code               = 'secondAssessmentInternalNote',
    source             = 'user',
    subsidy_stage_id   = '61436439-e337-4986-bc18-57138e2fab65',
    required_condition = null
WHERE "subsidy_stage_id" = '61436439-e337-4986-bc18-57138e2fab65' AND
      "code" = 'secondAssessmentInternalNote';

UPDATE public.fields
SET title              = 'Interne notitie',
    description        = null,
    type               = 'text',
    params             = '{"maxLength":null}',
    is_required        = false,
    code               = 'internalAssessmentInternalNote',
    source             = 'user',
    subsidy_stage_id   = '7ceb3c91-5c3b-4627-b9ef-a46d5fe2ed68',
    required_condition = null
WHERE "subsidy_stage_id" = '7ceb3c91-5c3b-4627-b9ef-a46d5fe2ed68' AND
      "code" = 'internalAssessmentInternalNote';

UPDATE public.fields
SET title              = 'Interne notitie',
    description        = null,
    type               = 'text',
    params             = '{"maxLength":null}',
    is_required        = false,
    code               = 'coordinatorImplementationInternalNote',
    source             = 'user',
    subsidy_stage_id   = '85ed726e-cdbe-444e-8d12-c56f9bed2621',
    required_condition = null
WHERE "subsidy_stage_id" = '85ed726e-cdbe-444e-8d12-c56f9bed2621' AND
      "code" = 'coordinatorImplementationInternalNote';

UPDATE public.fields
SET title              = 'Reden van afkeuring',
    description        = null,
    type               = 'text',
    params             = '{"maxLength":null}',
    is_required        = false,
    code               = 'coordinatorImplementationReasonForRejection',
    source             = 'user',
    subsidy_stage_id   = '85ed726e-cdbe-444e-8d12-c56f9bed2621',
    required_condition = null
WHERE "subsidy_stage_id" = '85ed726e-cdbe-444e-8d12-c56f9bed2621' AND
      "code" = 'coordinatorImplementationReasonForRejection';

UPDATE public.fields
SET title              = 'Extra informatie over de gedane wijzigingen',
    description        = null,
    type               = 'text',
    params             = '{"maxLength":null}',
    is_required        = false,
    code               = 'coordinatorImplementationApprovalNote',
    source             = 'user',
    subsidy_stage_id   = '85ed726e-cdbe-444e-8d12-c56f9bed2621',
    required_condition = null
WHERE "subsidy_stage_id" = '85ed726e-cdbe-444e-8d12-c56f9bed2621' AND
        "code" = 'coordinatorImplementationApprovalNote';

-- # Identical to the query on line 37
UPDATE public.subsidy_stage_uis
SET subsidy_stage_id = '8027c102-93ef-4735-ab66-97aa63b836eb',
    version          = 1,
    status           = 'published',
    input_ui         = '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Persoonsgegevens","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/personalDataChecklist","options":{"format":"checkbox-group"}}]}]},{"type":"Group","label":"Vaststellen WIA","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/wiaChecklist","options":{"format":"checkbox-group"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/WIADecisionIndicates","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/IVA_Or_WIA_Checklist","options":{"format":"checkbox-group"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/WIA_RejectedOnHighSalaryChecklist","options":{"format":"checkbox-group"}}]}]},{"type":"Group","label":"Zorgaanbieder en functie","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/employerChecklist","options":{"format":"checkbox-group"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/healthcareProviderStatementIsComplete","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/employerName","options":[]}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/healthcareProviderName","options":[]}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/chamberOfCommerceNumberHealthcareProvider","options":[]}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/healthcareProviderChecklist","options":{"format":"checkbox-group"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/healthcareProviderSBICode","options":[]}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/healthcareProviderAGBCode","options":[]}]}]},{"type":"Group","label":"Justiti\u00eble inrichting","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/judicialInstitutionIsEligible","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/applicantFoundInBigRegister","options":{"format":"radio"}}]}]},{"type":"Group","label":"Vaststellen post-COVID","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/postCovidChecklist","options":{"format":"checkbox-group"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/doctorFoundInBigRegister","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/doctorsCertificateIsComplete","options":{"format":"radio"}}]}]},{"type":"Group","label":"Uitkering","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/amount","options":{"format":"radio"}}]}]},{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessment","options":{"format":"radio"}}]}]},{"type":"Group","label":"Toelichting","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRequestedComplementReason","options":{"format":"radio"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRequestedComplementNote","options":{"format":"textarea","tip":"Deze notitie wordt opgenomen binnen de brief aan de aanvrager."},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRejectedNote","options":{"format":"textarea","tip":"Deze notitie wordt opgenomen binnen de brief aan de aanvrager."},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Afgekeurd"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentInternalNote","options":{"format":"textarea"}}]}]}]}',
    created_at       = null,
    updated_at       = null,
    view_ui          = '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"FormGroupControl","label":"Persoonsgegevens","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/personalDataChecklist","options":{"readonly":true,"format":"checkbox-group"}}]},{"type":"FormGroupControl","label":"Vaststellen WIA","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/wiaChecklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#\/properties\/WIADecisionIndicates","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/IVA_Or_WIA_Checklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#\/properties\/WIA_RejectedOnHighSalaryChecklist","options":{"readonly":true,"format":"checkbox-group"}}]},{"type":"FormGroupControl","label":"Zorgaanbieder en functie","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/employerChecklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#\/properties\/healthcareProviderStatementIsComplete","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/employerName","options":{"readonly":true}},{"type":"CustomControl","scope":"#\/properties\/healthcareProviderName","options":{"readonly":true}},{"type":"CustomControl","scope":"#\/properties\/chamberOfCommerceNumberHealthcareProvider","options":{"readonly":true}},{"type":"CustomControl","scope":"#\/properties\/healthcareProviderChecklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#\/properties\/healthcareProviderSBICode","options":{"readonly":true}},{"type":"CustomControl","scope":"#\/properties\/healthcareProviderAGBCode","options":{"readonly":true}}]},{"type":"FormGroupControl","label":"Justiti\u00eble inrichting","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/judicialInstitutionIsEligible","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/applicantFoundInBigRegister","options":{"readonly":true,"format":"radio"}}]},{"type":"FormGroupControl","label":"Vaststellen post-COVID","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/postCovidChecklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#\/properties\/doctorFoundInBigRegister","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/doctorsCertificateIsComplete","options":{"readonly":true,"format":"radio"}}]},{"type":"FormGroupControl","label":"Eerste beoordeling","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/amount","options":{"readonly":true}},{"type":"CustomControl","scope":"#\/properties\/firstAssessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRequestedComplementReason","options":{"readonly":true,"format":"radio"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}},{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRequestedComplementNote","options":{"readonly":true,"format":"textarea"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}},{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRejectedNote","options":{"readonly":true,"format":"textarea"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Afgekeurd"}}}},{"type":"CustomControl","scope":"#\/properties\/firstAssessmentInternalNote","options":{"readonly":true,"format":"textarea"}}]}]}'
WHERE id = '71f71916-c0ed-45bc-8186-1b4f5dfb69e8';
