UPDATE public.subsidy_stage_uis
SET view_ui = '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"FormGroupControl","label":"Persoonlijke informatie","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Voornaam":"{firstName}","Tussenvoegsel":"{infix}","Achternaam":"{lastName}","Geboortedatum":"{dateOfBirth}"}}}]},{"type":"FormGroupControl","label":"Adres","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Land":"{country}","Straatnaam":"{street}","Huisnummer":"{houseNumber}","Huisnummer toevoeging":"{houseNumberSuffix}","Postcode":"{postalCode}","Plaatsnaam":"{city}"}}}]},{"type":"FormGroupControl","label":"Contact","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Telefoonnummer":"{phoneNumber}","E-mailadres":"{email}"}}}]},{"type":"FormGroupControl","label":"Bank","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"IBAN":"{bankAccountNumber}","Naam rekeninghouder":"{bankAccountHolder}"}}}]},{"type":"FormGroupControl","label":"UWV","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Gewaarmerkt verzekeringsbericht":"{certifiedEmploymentDocument}"}}}]},{"type":"FormGroupControl","label":"WIA","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"WIA-Beslissing":"{wiaDecisionDocument}","WIA-Beslissing uitgesteld":"{isWiaDecisionPostponed}","Toekenningsbrief (indien uitgesteld)":"{wiaDecisionPostponedLetter}"}}}]},{"type":"FormGroupControl","label":"Werkgever","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Bewijs van dienstverband":"{employmentContract}","Functie":"{employmentFunction}","Werkgever":"{employerKind}","Verklaring zorgaanbieder (indien anders)":"{otherEmployerDeclarationFile}","Heeft u gewerkt in een justiti\u00eble inrichting?":"{hasBeenWorkingAtJudicialInstitution}","BIG-nummer":"{BIGNumberJudicialInstitution}"}}}]},{"type":"FormGroupControl","label":"Medisch","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Medisch onderzoeksverslag (medische rapportage)":"{socialMedicalAssessment}","Bevat uw medisch onderzoeksverslag de diagnose langdurige post-COVID?":"{hasPostCovidDiagnose}","Verklaring arts (indien geen diagnose)":"{doctorsCertificate}"}}}]}]}'
WHERE id = 'e6d5cd35-8c67-40c4-abc4-b1d6bf8afb97';
