-- ApplicationStageUISeeder::STAGE1_V1_UUID

UPDATE public.subsidy_stage_uis
SET input_ui = '{"type":"CustomPageNavigationControl","elements":[{"type":"CustomPageControl","label":"Start","elements":[{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"FormHtml","options":{"html":"<h2>Introductie<\/h2>\n<p>\n    Met dit formulier vraagt u een financi\u00eble ondersteuning aan via de regeling Zorgmedewerkers met langdurige post-COVID klachten. U kunt de financi\u00eble ondersteuning aanvragen als u voldoet aan de voorwaarden uit de regeling. De financi\u00eble ondersteuning bedraagt \u20ac24.010 per persoon.\n<\/p>\n<h2>Benodigde bestanden<\/h2>\n<p>Om in aanmerking te komen voor de financi\u00eble ondersteuning, vragen we u om een aantal documenten:<\/p>\n<p><\/p>\n<ol>\n    <li>Gewaarmerkt verzekeringsbericht UWV<\/li>\n    <li>Kopie WIA-beslissing<\/li>\n    <li>Bewijs van uw dienstverband<\/li>\n    <li>Medisch onderzoeksverslag \/ medische rapportage<\/li>\n<\/ol>\n\n<p>In de laatste stap van het aanvraagformulier kunt u de gevraagde documenten uploaden. In deze stap tonen we ook of mogelijk extra gegevens en\/of bewijsstukken noodzakelijk zijn voor de beoordeling van uw aanvraag. Let op: als we u om extra documenten vragen, gebruik dan altijd de <a\n    title=\"Documenten over Zorgmedewerkers met langdurige post-COVID klachten\"\n    href=\"https:\/\/www.dus-i.nl\/subsidies\/zorgmedewerkers-met-langdurige-post-covid-klachten\/documenten\"\n    target=\"_blank\"\n    rel=\"nofollow noopener external\"\n>verplichte formats<\/a\n>.<\/p>\n\n<h2>Meer informatie<\/h2>\n\n<p>Bekijk voor meer informatie over de regeling, de voorwaarden en de aanvraagprocedure onze <a title=\"website voor zorgmedewerkers met langdurige post-COVID klachten\" href=\"https:\/\/www.dus-i.nl\/post-covid\" target=\"_blank\" rel=\"nofollow noopener external\">website<\/a>. Heeft u een vraag? Gebruik dan het <a\n    title=\"Contactformulier DUS-i post-COVID\"\n    href=\"https:\/\/formulierdus-i.nl\/contact\/?kenmerk=PostCov&dus%2Di%5Furl=https%3A%2F%2Fwww%2Edus%2Di%2Enl%2Fsubsidies%2Fzorgmedewerkers%2Dmet%2Dlangdurige%2Dpost%2Dcovid%2Dklachten\"\n    target=\"_blank\"\n    rel=\"nofollow noopener external\"\n>contactformulier<\/a\n> of bel met 070-3405566. Wij zijn telefonisch bereikbaar op werkdagen van 9.00 tot 16.00 uur.<\/p>\n\n<h2>Aanvraag starten<\/h2>\n"}}]}],"options":{"required":[],"allOf":[]}},{"type":"CustomPageControl","label":"Persoonsgegevens toevoegen","elements":[{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"Group","label":"Persoonlijke informatie","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstName","label":"Voornaam","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/infix","label":"Tussenvoegsel","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/lastName","label":"Achternaam","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/dateOfBirth","label":"Geboortedatum","options":{"placeholder":""}}]}]},{"type":"Group","label":"Adres","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/street","label":"Straatnaam","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/houseNumber","label":"Huisnummer","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/houseNumberSuffix","label":"Huisnummer toevoeging","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/postalCode","label":"Postcode","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/city","label":"Plaatsnaam","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/country","label":"Land","options":{"format":"select","placeholder":"Selecteer een land","default":"Nederland"}}]}]},{"type":"Group","label":"Contact","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/phoneNumber","label":"Telefoonnummer","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/email","label":"E-mailadres","options":{"placeholder":"","tip":"U wordt via dit e-mailadres ge\u00efnformeerd over de status van uw aanvraag. Geef daarom alleen uw eigen e-mailadres door.","validation":["onBlur"]}}]}]},{"type":"Group","label":"Bank","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/bankAccountHolder","label":"Naam rekeninghouder","options":{"placeholder":"","validation":["onBlur"]}},{"type":"CustomControl","scope":"#\/properties\/bankAccountNumber","label":"IBAN","options":{"placeholder":"","tip":"Staat u onder bewind? Vermeld dan het IBAN van uw beheerrekening.","validation":["onValid"]}},{"type":"CustomControl","scope":"#\/properties\/bankStatement","label":"Bankafschrift","options":{"accept":"image\/jpeg,image\/png,application\/pdf","maxFileSize":20971520,"minItems":1,"maxItems":20,"tip":"De ingevoerde naam rekeninghouder en IBAN konden niet worden geverifieerd.\n\nOp de kopie van een recent bankafschrift moeten het rekeningnummer en uw naam zichtbaar zijn. Adres en datum mogen ook, maar zijn niet verplicht. Maak de andere gegevens onleesbaar. U mag ook een afdruk van internet bankieren gebruiken of een kopie van uw bankpas. Zie ook dit <a href=\"https:\/\/www.dus-i.nl\/documenten\/publicaties\/2018\/07\/30\/voorbeeld-bankafschrift\" target=\"_blank\">voorbeeld<\/a>.","validation":["onInput"]},"rule":{"effect":"SHOW","condition":{"scope":"#","schema":{"anyOf":[{"required":["validationResult"],"properties":{"validationResult":{"type":"object","required":["bankAccountNumber"],"properties":{"bankAccountNumber":{"type":"array","items":{"anyOf":[{"type":"object","required":["id"],"properties":{"id":{"const":"validationSurePayError"}}},{"type":"object","required":["id"],"properties":{"id":{"const":"validationSurePayWarning"}}}]}}}}}}]}}}}]}]}]}],"options":{"required":["firstName","lastName","street","dateOfBirth","houseNumber","postalCode","city","country","phoneNumber","email","bankAccountHolder","bankAccountNumber"],"allOf":[{"if":{"required":["validationResult"],"properties":{"validationResult":{"type":"object","required":["bankAccountNumber"],"properties":{"bankAccountNumber":{"type":"array","items":{"anyOf":[{"type":"object","required":["id"],"properties":{"id":{"const":"validationSurePayError"}}}]}}}}}},"then":{"required":["bankStatement"]}}]}},{"type":"CustomPageControl","label":"Documenten toevoegen","elements":[{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"Group","label":"UWV","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/certifiedEmploymentDocument","label":"Gewaarmerkt verzekeringsbericht","options":{"accept":"image\/jpeg,image\/png,.pdf","maxFileSize":20971520,"minItems":1,"maxItems":20,"tip":"Toegestane bestandstypen: pdf, jpg, jpeg, png. Maximale bestandsgrootte: 20 MB."}}]}]},{"type":"Group","label":"WIA","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/wiaDecisionDocument","label":"WIA-Beslissing","options":{"accept":"image\/jpeg,image\/png,.pdf","maxFileSize":20971520,"minItems":1,"maxItems":20,"tip":"Toegestane bestandstypen: pdf, jpg, jpeg, png. Maximale bestandsgrootte: 20 MB."}},{"type":"CustomControl","scope":"#\/properties\/isWiaDecisionPostponed","label":"Is uw WIA-beslissing uitgesteld vanwege een (vrijwillige of verplichte) loondoorbetaling?","options":{"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/wiaDecisionPostponedLetter","label":"Toekenningsbrief","options":{"accept":"image\/jpeg,image\/png,.pdf","maxFileSize":20971520,"minItems":1,"maxItems":20,"tip":"Toegestane bestandstypen: pdf, jpg, jpeg, png. Maximale bestandsgrootte: 20 MB."},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/isWiaDecisionPostponed","schema":{"const":"Ja"}}}},{"type":"CustomControl","scope":"#\/properties\/isWiaDecisionFirstSickDayOutsidePeriod","label":"Blijkt uit uw WIA-beslissing dat uw eerste ziektedag buiten de periode van 1 maart 2020 tot en met 31 december 2020 ligt?","options":{"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/wiaFirstSickDayOutsidePeriodProof","label":"Onderbouwing eerste ziektedag buiten de periode","options":{"accept":"image\/jpeg,image\/png,application\/pdf","maxFileSize":20971520,"minItems":1,"maxItems":20,"tip":"Upload een document waarmee u kunt aantonen dat uw langdurige Post-COVID klachten toch het gevolg zijn van een vermoedelijke COVID-19 besmetting in de periode van 1 maart 2020 tot en met 31 december 2020. U kunt dit bijvoorbeeld onderbouwen met een verklaring van uw (bedrijfs)arts. Toegestane bestandstypen: pdf, jpg, jpeg, png. Maximale bestandsgrootte: 20 MB."},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/isWiaDecisionFirstSickDayOutsidePeriod","schema":{"const":"Ja"}}}}]}]},{"type":"Group","label":"Werkgever","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/employmentContract","label":"Bewijs van uw dienstverband","options":{"accept":"image\/jpeg,image\/png,.pdf","maxFileSize":20971520,"minItems":1,"maxItems":20,"tip":"Toegestane bestandstypen: pdf, jpg, jpeg, png. Maximale bestandsgrootte: 20 MB."}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/employmentFunction","label":"Functie","options":{"format":"select","placeholder":"-- Selecteer een functie --"}}]},{"type":"CustomControl","scope":"#\/properties\/otherEmploymentFunction","label":"Andere functie","options":{"placeholder":""},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/employmentFunction","schema":{"const":"Anders"}}}},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/employerKind","label":"Werkgever","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/otherEmployerDeclarationFile","label":"Verklaring zorgaanbieder","options":{"accept":"image\/jpeg,image\/png,.pdf","maxFileSize":20971520,"minItems":1,"maxItems":20,"tip":"Toegestane bestandstypen: pdf, jpg, jpeg, png. Maximale bestandsgrootte: 20 MB."},"rule":{"effect":"SHOW","condition":{"scope":"#","schema":{"anyOf":[{"required":["employmentFunction"],"properties":{"employmentFunction":{"const":"Anders"}}},{"required":["employerKind"],"properties":{"employerKind":{"const":"Andere organisatie"}}}]}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/hasBeenWorkingAtJudicialInstitution","label":"Heeft u gewerkt in een justiti\u00eble inrichting?","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/BIGNumberJudicialInstitution","label":"BIG-nummer","options":{},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/hasBeenWorkingAtJudicialInstitution","schema":{"const":"Ja"}}}}]}]},{"type":"Group","label":"Medisch","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/socialMedicalAssessment","label":"Medisch onderzoeksverslag (medische rapportage)","options":{"accept":"image\/jpeg,image\/png,.pdf","maxFileSize":20971520,"minItems":1,"maxItems":20,"tip":"Toegestane bestandstypen: pdf, jpg, jpeg, png. Maximale bestandsgrootte: 20 MB."}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/hasPostCovidDiagnose","label":"Bevat uw medisch onderzoeksverslag de diagnose langdurige post-COVID?","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/doctorsCertificate","label":"Verklaring arts","options":{"accept":"image\/jpeg,image\/png,.pdf","maxFileSize":20971520,"minItems":1,"maxItems":20,"tip":"Toegestane bestandstypen: pdf, jpg, jpeg, png. Maximale bestandsgrootte: 20 MB."},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/hasPostCovidDiagnose","schema":{"const":"Nee"}}}}]}]}]}],"options":{"required":["certifiedEmploymentDocument","wiaDecisionDocument","isWiaDecisionPostponed","isWiaDecisionFirstSickDayOutsidePeriod","employmentContract","employmentFunction","employerKind","hasBeenWorkingAtJudicialInstitution","socialMedicalAssessment","hasPostCovidDiagnose"],"allOf":[{"if":{"properties":{"isWiaDecisionPostponed":{"const":"Ja"}}},"then":{"required":["wiaDecisionPostponedLetter"]}},{"if":{"properties":{"isWiaDecisionFirstSickDayOutsidePeriod":{"const":"Ja"}}},"then":{"required":["wiaFirstSickDayOutsidePeriodProof"]}},{"if":{"anyOf":[{"required":["employmentFunction"],"properties":{"employmentFunction":{"const":"Anders"}}},{"required":["employerKind"],"properties":{"employerKind":{"const":"Andere organisatie"}}}]},"then":{"required":["otherEmployerDeclarationFile"]}},{"if":{"properties":{"hasBeenWorkingAtJudicialInstitution":{"const":"Ja"}}},"then":{"required":["BIGNumberJudicialInstitution"]}},{"if":{"properties":{"employmentFunction":{"const":"Anders"}}},"then":{"required":["otherEmploymentFunction"]}},{"if":{"properties":{"hasPostCovidDiagnose":{"const":"Nee"}}},"then":{"required":["doctorsCertificate"]}}]}},{"type":"CustomPageControl","label":"Controleren en ondertekenen","elements":[{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"Group","label":"Controleren","elements":[{"type":"FormResultsTable","label":"Uw gegevens","options":{"fields":{"Naam":"{firstName} {infix} {lastName}","Adres":"{street} {houseNumber}{houseNumberSuffix} {postalCode} {city}","Telefoon":"{phoneNumber}","E-mailadres":"{email}","Geboortedatum":"{dateOfBirth}"}}}]},{"type":"Group","label":"Ondertekenen","elements":[{"type":"CustomControl","scope":"#\/properties\/truthfullyCompleted","label":"Waarheidsverklaring","options":{"description":"Ik verklaar het formulier naar waarheid te hebben ingevuld."}}]}]}],"options":{"required":["truthfullyCompleted"],"allOf":[]}}]}',
    view_ui = '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"FormGroupControl","label":"Persoonlijke informatie","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Voornaam":"{firstName}","Tussenvoegsel":"{infix}","Achternaam":"{lastName}","Geboortedatum":"{dateOfBirth}"},"showEmptyFields":true}}]},{"type":"FormGroupControl","label":"Adres","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Land":"{country}","Straatnaam":"{street}","Huisnummer":"{houseNumber}","Huisnummer toevoeging":"{houseNumberSuffix}","Postcode":"{postalCode}","Plaatsnaam":"{city}"}}}]},{"type":"FormGroupControl","label":"Contact","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Telefoonnummer":"{phoneNumber}","E-mailadres":"{email}"}}}]},{"type":"FormGroupControl","label":"Bank","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"IBAN":"{bankAccountNumber}","Naam rekeninghouder":"{bankAccountHolder}","Bankafschrift (indien nodig)":"{bankStatement}"}}}]},{"type":"FormGroupControl","label":"UWV","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Gewaarmerkt verzekeringsbericht":"{certifiedEmploymentDocument}"}}}]},{"type":"FormGroupControl","label":"WIA","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"WIA-Beslissing":"{wiaDecisionDocument}","WIA-Beslissing uitgesteld":"{isWiaDecisionPostponed}","Toekenningsbrief (indien uitgesteld)":"{wiaDecisionPostponedLetter}","Eerste ziektedag buiten de periode van 1 maart 2020 tot en met 31 december":"{isWiaDecisionFirstSickDayOutsidePeriod}","Bewijsstukken (eerste ziektedag buiten genoemde periode)":"{wiaFirstSickDayOutsidePeriodProof}"}}}]},{"type":"FormGroupControl","label":"Werkgever","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Bewijs van dienstverband":"{employmentContract}","Functie":"{employmentFunction}","Werkgever":"{employerKind}","Verklaring zorgaanbieder (indien anders)":"{otherEmployerDeclarationFile}","Heeft u gewerkt in een justiti\u00eble inrichting?":"{hasBeenWorkingAtJudicialInstitution}","BIG-nummer":"{BIGNumberJudicialInstitution}"}}}]},{"type":"FormGroupControl","label":"Medisch","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Medisch onderzoeksverslag (medische rapportage)":"{socialMedicalAssessment}","Bevat uw medisch onderzoeksverslag de diagnose langdurige post-COVID?":"{hasPostCovidDiagnose}","Verklaring arts (indien geen diagnose)":"{doctorsCertificate}"}}}]}]}'
WHERE id = '422cb32a-fed3-4d69-91ca-818db6b96daf';

-- AssessmentStageUISeeder::ASSESSMENT_STAGE2_UI_UUID

UPDATE public.subsidy_stage_uis
SET input_ui = '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Persoonsgegevens","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/personalDataChecklist","options":{"format":"checkbox-group"}}]}]},{"type":"Group","label":"Vaststellen WIA","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/wiaChecklist","options":{"format":"checkbox-group"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/WIADecisionIndicates","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/IVA_Or_WIA_Checklist","options":{"format":"checkbox-group"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/WIA_RejectedOnHighSalaryChecklist","options":{"format":"checkbox-group"}}]}]},{"type":"Group","label":"Zorgaanbieder en functie","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/employerChecklist","options":{"format":"checkbox-group"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/healthcareProviderStatementIsComplete","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/employerName","options":[]}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/healthcareProviderName","options":[]}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/chamberOfCommerceNumberHealthcareProvider","options":[]}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/healthcareProviderChecklist","options":{"format":"checkbox-group"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/healthcareProviderSBICode","options":[]}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/healthcareProviderAGBCode","options":[]}]}]},{"type":"Group","label":"Justiti\u00eble inrichting","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/judicialInstitutionIsEligible","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/applicantFoundInBigRegister","options":{"format":"radio"}}]}]},{"type":"Group","label":"Vaststellen post-COVID","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/postCovidChecklist","options":{"format":"checkbox-group"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/doctorFoundInBigRegister","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/doctorsCertificateIsComplete","options":{"format":"radio"}}]}]},{"type":"Group","label":"Uitkering","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/amount","options":{"format":"radio"}}]}]},{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessment","options":{"format":"radio"}}]}]},{"type":"Group","label":"Toelichting","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRequestedComplementReason","options":{"format":"radio"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRequestedComplementNote","options":{"format":"textarea","tip":"Deze notitie wordt opgenomen binnen de brief aan de aanvrager."},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRejectedNote","options":{"format":"textarea","tip":"Deze notitie wordt opgenomen binnen de brief aan de aanvrager."},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Afgekeurd"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentApprovedNote","options":{"format":"textarea","tip":"Deze notitie wordt opgenomen binnen de brief aan de aanvrager."},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Goedgekeurd"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentInternalNote","options":{"format":"textarea"}}]}]}]}',
    view_ui = '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"FormGroupControl","label":"Persoonsgegevens","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/personalDataChecklist","options":{"readonly":true,"format":"checkbox-group"}}]},{"type":"FormGroupControl","label":"Vaststellen WIA","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/wiaChecklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#\/properties\/WIADecisionIndicates","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/IVA_Or_WIA_Checklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#\/properties\/WIA_RejectedOnHighSalaryChecklist","options":{"readonly":true,"format":"checkbox-group"}}]},{"type":"FormGroupControl","label":"Zorgaanbieder en functie","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/employerChecklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#\/properties\/healthcareProviderStatementIsComplete","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/employerName","options":{"readonly":true}},{"type":"CustomControl","scope":"#\/properties\/healthcareProviderName","options":{"readonly":true}},{"type":"CustomControl","scope":"#\/properties\/chamberOfCommerceNumberHealthcareProvider","options":{"readonly":true}},{"type":"CustomControl","scope":"#\/properties\/healthcareProviderChecklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#\/properties\/healthcareProviderSBICode","options":{"readonly":true}},{"type":"CustomControl","scope":"#\/properties\/healthcareProviderAGBCode","options":{"readonly":true}}]},{"type":"FormGroupControl","label":"Justiti\u00eble inrichting","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/judicialInstitutionIsEligible","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/applicantFoundInBigRegister","options":{"readonly":true,"format":"radio"}}]},{"type":"FormGroupControl","label":"Vaststellen post-COVID","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/postCovidChecklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#\/properties\/doctorFoundInBigRegister","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/doctorsCertificateIsComplete","options":{"readonly":true,"format":"radio"}}]},{"type":"FormGroupControl","label":"Eerste beoordeling","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#\/properties\/amount","options":{"readonly":true}},{"type":"CustomControl","scope":"#\/properties\/firstAssessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRequestedComplementReason","options":{"readonly":true,"format":"radio"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}},{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRequestedComplementNote","options":{"readonly":true,"format":"textarea"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}},{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRejectedNote","options":{"readonly":true,"format":"textarea"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Afgekeurd"}}}},{"type":"CustomControl","scope":"#\/properties\/firstAssessmentApprovedNote","options":{"readonly":true,"format":"textarea"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Goedgekeurd"}}}},{"type":"CustomControl","scope":"#\/properties\/firstAssessmentInternalNote","options":{"readonly":true,"format":"textarea"}}]}]}'
WHERE id = '26a15797-01ec-4d19-b10d-618c0507c36f';

-- AssessmentStageUISeeder::ASSESSMENT_STAGE3_UI_UUID

UPDATE public.subsidy_stage_uis
SET input_ui = '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessorMotivatedValid","options":{"format":"checkbox"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/secondAssessment","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/secondAssessmentInternalNote","options":{"format":"textarea"}}]}]}]}',
    view_ui = '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessorMotivatedValid","options":{"readonly":true}},{"type":"CustomControl","scope":"#\/properties\/secondAssessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/secondAssessmentInternalNote","options":{"readonly":true,"format":"textarea"}}]}'
WHERE id = '31b8c514-10eb-4e2b-a882-04b8dc2d3711';

-- AssessmentStageUISeeder::ASSESSMENT_STAGE4_UI_UUID

UPDATE public.subsidy_stage_uis
SET input_ui = '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Checklist","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/internalAssessmentChecklist","options":{"format":"checkbox-group"}}]}]},{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/internalAssessment","options":{"format":"radio"}}]}]},{"type":"Group","label":"Toelichting","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/internalAssessmentInternalNote","options":{"format":"textarea"}}]}]}]}',
    view_ui = '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"CustomControl","scope":"#\/properties\/internalAssessmentChecklist","options":{"readonly":true,"format":"checkbox-group"}},{"type":"CustomControl","scope":"#\/properties\/internalAssessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/internalAssessmentInternalNote","options":{"readonly":true,"format":"textarea"}}]}'
WHERE id = '47df4a63-6bfe-49da-a684-2e948d9c4c50';

-- AssessmentStageUISeeder::ASSESSMENT_STAGE5_UI_UUID

UPDATE public.subsidy_stage_uis
SET input_ui = '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/implementationCoordinatorAssessment","options":{"format":"radio"}}]}]},{"type":"Group","label":"Toelichting","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/coordinatorImplementationReasonForRejection","options":{"format":"textarea","tip":"Deze notitie wordt opgenomen binnen de brief aan de aanvrager."},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/implementationCoordinatorAssessment","schema":{"const":"Afgekeurd"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/coordinatorImplementationInternalNote","options":{"format":"textarea"}}]}]}]}',
    view_ui = '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"CustomControl","scope":"#\/properties\/implementationCoordinatorAssessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/coordinatorImplementationInternalNote","options":{"readonly":true,"format":"textarea"}}]}'
WHERE id = '30c57b87-dd07-423e-908e-39865c10ef21';

