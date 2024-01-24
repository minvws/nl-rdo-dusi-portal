DELETE FROM public.fields
WHERE subsidy_stage_id IN ('8027c102-93ef-4735-ab66-97aa63b836eb', '61436439-E337-4986-BC18-57138E2FAB65',
                             '7CEB3C91-5C3B-4627-B9EF-A46D5FE2ED68', '85ED726E-CDBE-444E-8D12-C56F9BED2621');

INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition)
VALUES (public.uuid_generate_v4(), 'Controlevragen', null, 'multiselect',
        '{"options":["Alle aangeleverde documenten zijn te herleiden tot dezelfde persoon op basis van BSN en de overige persoonsgegevens","Het IBAN bestaat en is actief","Het opgegeven IBAN staat op naam van de aanvrager of bewindvoerder","Op basis van de SurePay terugkoppeling ben ik akkoord met het opgegeven rekeningnummer"]}',
        false, 'personalDataChecklist', 'user', '8027c102-93ef-4735-ab66-97aa63b836eb', null);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition)
VALUES (public.uuid_generate_v4(), 'Algemeen', null, 'multiselect',
        '{"options":["Het verzekeringsbericht is gewaarmerkt en het BSN is zichtbaar in de upload","Het BSN op het verzekeringsbericht komt overeen met dat van de aanvrager"]}',
        false, 'wiaChecklist', 'user', '8027c102-93ef-4735-ab66-97aa63b836eb', null);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition)
VALUES (public.uuid_generate_v4(), 'Uit de WIA-beslissing blijkt dat er sprake is van', null, 'select',
        '{"options":["IVA uitkering","WGA uitkering","Geen WIA-uitkering met als reden dat meer dan 65% verdiend kan worden"],"default":null}',
        false, 'WIADecisionIndicates', 'user', '8027c102-93ef-4735-ab66-97aa63b836eb', null);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition)
VALUES (public.uuid_generate_v4(),
        'Uit de toekenningsbrief van de afgesproken loondoorbetaling blijkt dat de eerste ziektedag in de periode van 1 maart 2020 en 1 juli 2020 ligt',
        null, 'select', '{"options":["Nee","Ja","Niet van toepassing"],"default":null}', false,
        'firstSickDayWithinExpiryDate', 'user', '8027c102-93ef-4735-ab66-97aa63b836eb', null);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition)
VALUES (public.uuid_generate_v4(), 'IVA- of WGA-uitkering', null, 'multiselect',
        '{"options":["Niet van toepassing","Op het verzekeringsbericht staat vermeld dat de aanvrager een WIA-uitkering ontvangt","De ingangsdatum van de WIA in de WIA-beslissing komt overeen met de ingangsdatum op het verzekeringsbericht","De eerste ziektedag ligt in de periode van de eerste golf (1 maart 2020 tot 1 juli 2020)"]}',
        false, 'IVA_Or_WIA_Checklist', 'user', '8027c102-93ef-4735-ab66-97aa63b836eb', null);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition)
VALUES (public.uuid_generate_v4(), 'Geen WIA-uitkering met als reden dat meer dan 65% verdiend kan worden',
        null, 'multiselect',
        '{"options":["Niet van toepassing","De datum waarop de WIA-uitkering niet wordt ontvangen ligt in de periode van 1 maart 2022 tot 1 september 2022 (104 weken wachttijd)"]}',
        false, 'WIA_RejectedOnHighSalaryChecklist', 'user', '8027c102-93ef-4735-ab66-97aa63b836eb', null);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition)
VALUES (public.uuid_generate_v4(), 'Controlevragen', null, 'multiselect',
        '{"options":["De werkgever komt overeen met de laatste werkgever v\u00f3\u00f3r de WIA in het verzekeringsbericht","Uit de arbeidsovereenkomst en\/of de verklaring van de zorgaanbieder blijkt dat er sprake is van werkzaamheden die binnen de subsidieregeling vallen"]}',
        false, 'employerChecklist', 'user', '8027c102-93ef-4735-ab66-97aa63b836eb', null);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition)
VALUES (public.uuid_generate_v4(), 'De verklaring van de zorgaanbieder is volledig ingevuld', null,
        'select', '{"options":["Nee","Ja","Niet van toepassing"],"default":null}', false,
        'healthcareProviderStatementIsComplete', 'user', '8027c102-93ef-4735-ab66-97aa63b836eb', null);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition)
VALUES (public.uuid_generate_v4(), 'Naam werkgever', null, 'text', '{"maxLength":null}', false,
        'employerName', 'user', '8027c102-93ef-4735-ab66-97aa63b836eb', null);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition)
VALUES (public.uuid_generate_v4(), 'Naam zorgaanbieder, indien niet werkgever', null, 'text',
        '{"maxLength":null}', false, 'healthcareProviderName', 'user', '8027c102-93ef-4735-ab66-97aa63b836eb', null);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition)
VALUES (public.uuid_generate_v4(), 'KVK-nummer van de zorgaanbieder waar de zorg is verleend', null,
        'text', '{"maxLength":null}', false, 'chamberOfCommerceNumberHealthcareProvider', 'user',
        '8027c102-93ef-4735-ab66-97aa63b836eb', null);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition)
VALUES (public.uuid_generate_v4(), 'Controlevragen', null, 'multiselect',
        '{"options":["De zorgaanbieder waar de aanvrager werkzaam is geweest heeft de juiste SBI-code","De zorgaanbieder waar de aanvrager werkzaam is geweest heeft de juiste AGB code of is een Jeugdhulp aanbieder die op de lijst staat","De zorgaanbieder voldoet aan de eisen binnen de regeling"]}',
        false, 'healthcareProviderChecklist', 'user', '8027c102-93ef-4735-ab66-97aa63b836eb', null);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition)
VALUES (public.uuid_generate_v4(), 'SBI-code zorgaanbieder', null, 'text', '{"maxLength":100}', false,
        'healthcareProviderSBICode', 'user', '8027c102-93ef-4735-ab66-97aa63b836eb', null);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition)
VALUES (public.uuid_generate_v4(), 'AGB-code zorgaanbieder', null, 'text', '{"maxLength":100}', false,
        'healthcareProviderAGBCode', 'user', '8027c102-93ef-4735-ab66-97aa63b836eb', null);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition)
VALUES (public.uuid_generate_v4(),
        'De justitiële inrichting waar de aanvrager werkzaam is geweest valt binnen de regeling', null, 'select',
        '{"options":["Nee","Ja","Niet van toepassing"],"default":null}', false, 'judicialInstitutionIsEligible', 'user',
        '8027c102-93ef-4735-ab66-97aa63b836eb', null);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition)
VALUES (public.uuid_generate_v4(),
        'De aanvrager is op basis van het doorgegeven BIG-nummer terug te vinden in het BIG-register', null, 'select',
        '{"options":["Nee","Ja","Niet van toepassing"],"default":null}', false, 'applicantFoundInBigRegister', 'user',
        '8027c102-93ef-4735-ab66-97aa63b836eb', null);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition)
VALUES (public.uuid_generate_v4(), 'Controlevragen', null, 'multiselect',
        '{"options":["Op basis van het sociaal-medisch verslag en\/of de verklaring van de arts is vast te stellen dat er een post-COVID diagnose is gesteld","De post-COVID diagnose is v\u00f3\u00f3r 1 juni 2023 gesteld"]}',
        false, 'postCovidChecklist', 'user', '8027c102-93ef-4735-ab66-97aa63b836eb', null);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition)
VALUES (public.uuid_generate_v4(),
        'De arts die de verklaring heeft afgegeven is als arts geregistreerd in het BIG-register', null, 'select',
        '{"options":["Nee","Ja","Niet van toepassing"],"default":null}', false, 'doctorFoundInBigRegister', 'user',
        '8027c102-93ef-4735-ab66-97aa63b836eb', null);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition)
VALUES (public.uuid_generate_v4(), 'De verklaring van de arts is volledig ingevuld', null, 'select',
        '{"options":["Nee","Ja","Niet van toepassing"],"default":null}', false, 'doctorsCertificateIsComplete', 'user',
        '8027c102-93ef-4735-ab66-97aa63b836eb', null);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition)
VALUES (public.uuid_generate_v4(), 'Bedrag', null, 'select',
        '{"options":["\u20ac 15.000"],"default":null}', false, 'amount', 'user', '8027c102-93ef-4735-ab66-97aa63b836eb',
        null);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition)
VALUES (public.uuid_generate_v4(), 'Beoordeling', null, 'select',
        '{"options":["Aanvulling nodig","Afgekeurd","Goedgekeurd"],"default":null}', true, 'firstAssessment', 'user',
        '8027c102-93ef-4735-ab66-97aa63b836eb', null);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition)
VALUES (public.uuid_generate_v4(), 'Reden', null, 'select',
        '{"options":["Incomplete aanvraag","Onduidelijkheid of vervolgvragen"],"default":null}', false,
        'firstAssessmentRequestedComplementReason', 'user', '8027c102-93ef-4735-ab66-97aa63b836eb', null);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition)
VALUES (public.uuid_generate_v4(), 'Toelichting van benodigde aanvullingen', null, 'text',
        '{"maxLength":null}', false, 'firstAssessmentRequestedComplementNote', 'user',
        '8027c102-93ef-4735-ab66-97aa63b836eb', null);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition)
VALUES (public.uuid_generate_v4(), 'Reden van afkeuring', null, 'text', '{"maxLength":null}', false,
        'firstAssessmentRejectedNote', 'user', '8027c102-93ef-4735-ab66-97aa63b836eb', null);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition)
VALUES (public.uuid_generate_v4(), 'Interne notitie', null, 'text', '{"maxLength":null}', false,
        'firstAssessmentInternalNote', 'user', '8027c102-93ef-4735-ab66-97aa63b836eb', null);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition)
VALUES (public.uuid_generate_v4(), 'De motivatie van de eerste behandelaar is duidelijk en correct', null,
        'checkbox', 'null', false, 'firstAssessorMotivatedValid', 'user', '61436439-e337-4986-bc18-57138e2fab65', null);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition)
VALUES (public.uuid_generate_v4(), 'Beoordeling', null, 'select',
        '{"options":["Oneens met de eerste beoordeling","Eens met de eerste beoordeling"],"default":null}', true,
        'secondAssessment', 'user', '61436439-e337-4986-bc18-57138e2fab65', null);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition)
VALUES (public.uuid_generate_v4(), 'Interne notitie', null, 'text', '{"maxLength":null}', false,
        'secondAssessmentInternalNote', 'user', '61436439-e337-4986-bc18-57138e2fab65', null);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition)
VALUES (public.uuid_generate_v4(), 'Controlevragen', null, 'multiselect',
        '{"options":["Alle benodigde documenten zijn aangeleverd","Uit de dataverificatie blijkt dat er geen onvolkomenheden zijn geconstateerd","De motivatie van de eerste beoordeling is duidelijk"]}',
        false, 'internalAssessmentChecklist', 'user', '7ceb3c91-5c3b-4627-b9ef-a46d5fe2ed68', null);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition)
VALUES (public.uuid_generate_v4(), 'Beoordeling', null, 'select',
        '{"options":["Afgekeurd","Goedgekeurd"],"default":null}', true, 'internalAssessment', 'user',
        '7ceb3c91-5c3b-4627-b9ef-a46d5fe2ed68', null);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition)
VALUES (public.uuid_generate_v4(), 'Interne notitie', null, 'text', '{"maxLength":null}', false,
        'internalAssessmentInternalNote', 'user', '7ceb3c91-5c3b-4627-b9ef-a46d5fe2ed68', null);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition)
VALUES (public.uuid_generate_v4(), 'Beoordeling', null, 'select',
        '{"options":["Afgekeurd","Goedgekeurd"],"default":null}', true, 'implementationCoordinatorAssessment', 'user',
        '85ed726e-cdbe-444e-8d12-c56f9bed2621', null);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition)
VALUES (public.uuid_generate_v4(), 'Interne notitie', null, 'text', '{"maxLength":null}', false,
        'coordinatorImplementationInternalNote', 'user', '85ed726e-cdbe-444e-8d12-c56f9bed2621', null);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition)
VALUES (public.uuid_generate_v4(), 'Reden van afkeuring', null, 'text', '{"maxLength":null}', false,
        'coordinatorImplementationReasonForRejection', 'user', '85ed726e-cdbe-444e-8d12-c56f9bed2621', null);
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition)
VALUES (public.uuid_generate_v4(), 'Extra informatie over de gedane wijzigingen', null, 'text',
        '{"maxLength":null}', false, 'coordinatorImplementationApprovalNote', 'user',
        '85ed726e-cdbe-444e-8d12-c56f9bed2621', null);


DELETE FROM public.subsidy_stage_uis
WHERE id IN ('71F71916-C0ED-45BC-8186-1B4F5DFB69E8', '44914BC7-9E4F-4B79-9498-01ADBE5C4CFE',
               'E819DF05-03B7-4F37-B315-7F62339FD067', 'C51302F6-E131-45FF-8D4B-F4FF4A39B52F');

INSERT INTO public.subsidy_stage_uis (id, subsidy_stage_id, version, status, input_ui, created_at, updated_at, view_ui)
VALUES ('71f71916-c0ed-45bc-8186-1b4f5dfb69e8', '8027c102-93ef-4735-ab66-97aa63b836eb', 1, 'published',
        '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Persoonsgegevens","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/personalDataChecklist","options":{"format":"checkbox-group"}}]}]},{"type":"Group","label":"Vaststellen WIA","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/wiaChecklist","options":{"format":"checkbox-group"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/WIADecisionIndicates","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstSickDayWithinExpiryDate","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/IVA_Or_WIA_Checklist","options":{"format":"checkbox-group"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/WIA_RejectedOnHighSalaryChecklist","options":{"format":"checkbox-group"}}]}]},{"type":"Group","label":"Zorgaanbieder en functie","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/employerChecklist","options":{"format":"checkbox-group"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/healthcareProviderStatementIsComplete","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/employerName","options":[]}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/healthcareProviderName","options":[]}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/chamberOfCommerceNumberHealthcareProvider","options":[]}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/healthcareProviderChecklist","options":{"format":"checkbox-group"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/healthcareProviderSBICode","options":[]}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/healthcareProviderAGBCode","options":[]}]}]},{"type":"Group","label":"Justiti\u00eble inrichting","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/judicialInstitutionIsEligible","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/applicantFoundInBigRegister","options":{"format":"radio"}}]}]},{"type":"Group","label":"Vaststellen post-COVID","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/postCovidChecklist","options":{"format":"checkbox-group"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/doctorFoundInBigRegister","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/doctorsCertificateIsComplete","options":{"format":"radio"}}]}]},{"type":"Group","label":"Uitkering","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/amount","options":{"format":"radio"}}]}]},{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessment","options":{"format":"radio"}}]}]},{"type":"Group","label":"Toelichting","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRequestedComplementReason","options":{"format":"radio"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRequestedComplementNote","options":{"tip":"Toelichting: Deze notitie wordt opgenomen binnen de brief aan de aanvrager."},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRejectedNote","options":{"tip":"Toelichting: Deze notitie wordt opgenomen binnen de brief aan de aanvrager."},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Afgekeurd"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentInternalNote","options":[]}]}]}]}',
        null, null,
        '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"FormGroupControl","label":"Persoonsgegevens","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/personalDataChecklist","options":{"readonly":true,"format":"checkbox-group"}}]},{"type":"FormGroupControl","label":"Vaststellen WIA","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/wiaChecklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#\/properties\/WIADecisionIndicates","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/firstSickDayWithinExpiryDate","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/IVA_Or_WIA_Checklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#\/properties\/WIA_RejectedOnHighSalaryChecklist","options":{"readonly":true,"format":"checkbox-group"}}]},{"type":"FormGroupControl","label":"Zorgaanbieder en functie","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/employerChecklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#\/properties\/healthcareProviderStatementIsComplete","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/employerName","options":{"readonly":true}},{"type":"CustomControl","scope":"#\/properties\/healthcareProviderName","options":{"readonly":true}},{"type":"CustomControl","scope":"#\/properties\/chamberOfCommerceNumberHealthcareProvider","options":{"readonly":true}},{"type":"CustomControl","scope":"#\/properties\/healthcareProviderChecklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#\/properties\/healthcareProviderSBICode","options":{"readonly":true}},{"type":"CustomControl","scope":"#\/properties\/healthcareProviderAGBCode","options":{"readonly":true}}]},{"type":"FormGroupControl","label":"Justiti\u00eble inrichting","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/judicialInstitutionIsEligible","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/applicantFoundInBigRegister","options":{"readonly":true,"format":"radio"}}]},{"type":"FormGroupControl","label":"Vaststellen post-COVID","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/postCovidChecklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#\/properties\/doctorFoundInBigRegister","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/doctorsCertificateIsComplete","options":{"readonly":true,"format":"radio"}}]},{"type":"FormGroupControl","label":"Eerste beoordeling","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/amount","options":{"readonly":true}},{"type":"CustomControl","scope":"#\/properties\/firstAssessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRequestedComplementReason","options":{"readonly":true,"format":"radio"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}},{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRequestedComplementNote","options":{"readonly":true},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}},{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRejectedNote","options":{"readonly":true},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Afgekeurd"}}}},{"type":"CustomControl","scope":"#\/properties\/firstAssessmentInternalNote","options":{"readonly":true}}]}]}');
INSERT INTO public.subsidy_stage_uis (id, subsidy_stage_id, version, status, input_ui, created_at, updated_at, view_ui)
VALUES ('44914bc7-9e4f-4b79-9498-01adbe5c4cfe', '61436439-e337-4986-bc18-57138e2fab65', 1, 'published',
        '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessorMotivatedValid","options":{"format":"checkbox"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/secondAssessment","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/secondAssessmentInternalNote","options":[]}]}]}]}',
        null, null,
        '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessorMotivatedValid","options":{"readonly":true}},{"type":"CustomControl","scope":"#\/properties\/secondAssessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/secondAssessmentInternalNote","options":{"readonly":true}}]}');
INSERT INTO public.subsidy_stage_uis (id, subsidy_stage_id, version, status, input_ui, created_at, updated_at, view_ui)
VALUES ('e819df05-03b7-4f37-b315-7f62339fd067', '7ceb3c91-5c3b-4627-b9ef-a46d5fe2ed68', 1, 'published',
        '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Checklist","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/internalAssessmentChecklist","options":{"format":"checkbox-group"}}]}]},{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/internalAssessment","options":{"format":"radio"}}]}]},{"type":"Group","label":"Toelichting","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/internalAssessmentInternalNote","options":[]}]}]}]}',
        null, null,
        '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"CustomControl","scope":"#\/properties\/internalAssessmentChecklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#\/properties\/internalAssessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/internalAssessmentInternalNote","options":{"readonly":true}}]}');
INSERT INTO public.subsidy_stage_uis (id, subsidy_stage_id, version, status, input_ui, created_at, updated_at, view_ui)
VALUES ('c51302f6-e131-45ff-8d4b-f4ff4a39b52f', '85ed726e-cdbe-444e-8d12-c56f9bed2621', 1, 'published',
        '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/implementationCoordinatorAssessment","options":{"format":"radio"}}]}]},{"type":"Group","label":"Toelichting","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/coordinatorImplementationReasonForRejection","options":{"tip":"Toelichting:  Deze notitie wordt opgenomen binnen de brief aan de aanvrager."},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/implementationCoordinatorAssessment","schema":{"const":"Afgekeurd"}}}},{"type":"CustomControl","scope":"#\/properties\/coordinatorImplementationApprovalNote","options":{"tip":"Toelichting:  Deze notitie wordt opgenomen binnen de brief aan de aanvrager."},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/implementationCoordinatorAssessment","schema":{"const":"Goedgekeurd"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/coordinatorImplementationInternalNote","options":[]}]}]}]}',
        null, null,
        '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"CustomControl","scope":"#\/properties\/implementationCoordinatorAssessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/implementationCoordinatorInternalNote","options":{"readonly":true}}]}');
