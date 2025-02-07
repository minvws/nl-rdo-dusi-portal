INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition, retention_period_on_approval, exclude_from_clone_data)
VALUES ('69700e65-fec0-4846-aa6d-ea8e850220b8', 'Zakenpartnernummer', null, 'text:numeric',
        '{"maxLength": 20}', false, 'businessPartnerNumber', 'user', '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c',
        null, 'short', false);

INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition, retention_period_on_approval, exclude_from_clone_data)
VALUES ('8d180a3f-61f6-4904-97e8-2a0ca4017374', 'Verplichtingennummer', null, 'text:numeric',
        '{"maxLength": 20}', false, 'liabilitiesNumber', 'user', '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c',
        null, 'short', false);


UPDATE public.fields
SET params = '{"options": ["De aanvrager heeft niet eerder een BTV-subsidieaanvraag ingediend", "De aanvrager komt niet voor in het M&O register", "De persoonsgegevens zijn door de aanvrager juist ingevuld (NAW-gegevens, IBAN)", "Uittreksel van het BRP is opgestuurd (< 12 maanden)", "De aanvrager is een ingezetene (> 4 maanden) in Nederland", "De aanvrager is ouder dan 18 jaar", "De ingevoerde persoonsgegevens zijn conform het BRP uittreksel", "De medische verklaringen zijn volledig ingevuld en op naam van de aanvrager", "De verklaring van de arts over het behandeltraject is minder dan 2 maanden oud", "De verklaring van de arts over het behandeltraject is ondertekend en voorzien van een naamstempel", "Het opgegeven BIG-nummer komt overeen met het BIG-register", "De aanvrager heeft genderdysforie", "De aanvrager heeft minimaal een jaar voor de aanvraag hormoonbehandeling ondergaan, of is hiermee vanwege medische redenen gestopt of kon deze om medische redenen niet ondergaan", "De verklaring van de arts met de vermelding van het type behandeling is opgestuurd (<12 maanden oud)", "De verklaring van de arts met de vermelding van de type behandeling is ondertekend en voorzien van een naamstempel", "De type behandeling voldoet aan de voorwaarden conform de subsidieregeling", "Het opgegeven IBAN is correct", "De verificatiebevestiging met betrekking tot de verklaring over het behandeltraject is ontvangen", "De verificatiebevestiging met betrekking tot de verklaring over het type behandeling is ontvangen"]}'
WHERE code = 'firstAssessmentChecklist' AND subsidy_stage_id = '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c';

UPDATE public.fields
SET params = '{"options": ["Valt de aanvrager onder de WSNP/bewindvoering?", "Alle benodigde documenten zijn aangeleverd", "Het subsidiebedrag klopt met de gekozen behandeling", "De aanvraag kan verleend worden", "Het subsidiebedrag is juist vermeld in het Portaal en in de verplichting in SAP", "Het IBAN is juist vermeld in het Portaal en in de verplichting in SAP", "De verplichting is juist in SAP geboekt", "De verplichting is in SAP goedgekeurd", "De verleningsbeschikking mag verzonden worden"]}'
WHERE code = 'internalAssessmentChecklist' AND subsidy_stage_id = 'e456e790-1919-4a2b-b3d5-337d0053abe3';

UPDATE public.subsidy_stage_uis
SET input_ui = '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Beoordeling","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentChecklist","options":{"format":"checkbox-group"}}]}]},{"type":"Group","label":"Financi\u00eble afhandeling","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/businessPartnerNumber","label":"Zakenpartnernummer","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/liabilitiesNumber","label":"Verplichtingennummer","options":{"placeholder":""}}]}]},{"type":"Group","label":"Uitkering","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/amount","options":{"format":"radio"}}]}]},{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessment","options":{"format":"radio"}}]}]},{"type":"Group","label":"Toelichting","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRequestedComplementReason","options":{"format":"radio"},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRequestedComplementNote","options":{"format":"textarea","tip":"Deze notitie wordt opgenomen binnen de brief aan de aanvrager."},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Aanvulling nodig"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentRejectedNote","options":{"format":"textarea","tip":"Deze notitie wordt opgenomen binnen de brief aan de aanvrager."},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Afgekeurd"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentApprovedNote","options":{"format":"textarea","tip":"Deze notitie wordt opgenomen binnen de brief aan de aanvrager."},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/firstAssessment","schema":{"const":"Goedgekeurd"}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessmentInternalNote","options":{"format":"textarea"}}]}]}]}',
    view_ui = '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"FormGroupControl","label":"Persoonsgegevens","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#/properties/firstAssessmentChecklist","options":{"readonly":true,"format":"checkbox-group"}}]},{"type":"FormGroupControl","label":"Financiële afhandeling","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#/properties/businessPartnerNumber","label":"Zakenpartnernummer","options":{"readonly":true}},{"type":"CustomControl","scope":"#/properties/liabilitiesNumber","label":"Verplichtingennummer","options":{"readonly":true}}]},{"type":"FormGroupControl","label":"Uitkering","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#/properties/amount","options":{"readonly":true}}]},{"type":"FormGroupControl","label":"Eerste beoordeling","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"CustomControl","scope":"#/properties/firstAssessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#/properties/firstAssessmentRequestedComplementReason","options":{"readonly":true,"format":"radio"},"rule":{"effect":"SHOW","condition":{"scope":"#/properties/firstAssessment","schema":{"const":"Aanvulling nodig"}}}},{"type":"CustomControl","scope":"#/properties/firstAssessmentRequestedComplementNote","options":{"readonly":true,"format":"textarea"},"rule":{"effect":"SHOW","condition":{"scope":"#/properties/firstAssessment","schema":{"const":"Aanvulling nodig"}}}},{"type":"CustomControl","scope":"#/properties/firstAssessmentRejectedNote","options":{"readonly":true,"format":"textarea"},"rule":{"effect":"SHOW","condition":{"scope":"#/properties/firstAssessment","schema":{"const":"Afgekeurd"}}}},{"type":"CustomControl","scope":"#/properties/firstAssessmentApprovedNote","options":{"readonly":true,"format":"textarea"},"rule":{"effect":"SHOW","condition":{"scope":"#/properties/firstAssessment","schema":{"const":"Goedgekeurd"}}}},{"type":"CustomControl","scope":"#/properties/firstAssessmentInternalNote","options":{"readonly":true,"format":"textarea"}}]}]}'
where id = '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c';

UPDATE public.subsidy_stage_transition_messages
SET content_pdf = e'{layout \'letter_layout.latte\'}

{block concern}
    Betreft: Verlening aanvraag {$content->subsidyTitle}
{/block}

{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>
    <p>&nbsp;</p>

    <h2>Besluit</h2>
    <p>
        Uw subsidieaanvraag borstprothesen transvrouwen is toegewezen. De hoogte van de subsidie is
        {$content->stage2->amount}.
    </p>
    <p>
        De subsidie is gebaseerd op artikel 4 van de subsidieregeling borstprothesen transvrouwen. De
        subsidieregeling heeft als doel dat man-vrouw transgenders met genderdysforie, die zich in een medisch
        transitietraject bevinden, door een vergroting van de borsten een vrouwelijk(er) profiel kunnen krijgen.
    </p>
    <p>
       Volgens artikel 8 van de subsidieregeling ontvangt u een voorschot van 100% van het subsidiebedrag. De
       operatie dient in beginsel binnen een jaar na de aanvraag van de subsidie verricht te zijn. Als dit door
       omstandigheden niet mogelijk is gebleken, kan ontheffing of vrijstelling van deze termijn verleend worden.
    </p>
    <p>&nbsp;</p>

    <h2>Motivering bij besluit</h2>
    <p>
        Op grond van uw aanvraag en de verklaring ken ik u de subsidie borstprothesen transvrouwen toe.
    </p>

    {if $content->stage2->firstAssessmentApprovedNote}
        <p>{$content->stage2->firstAssessmentApprovedNote|breakLines}</p>
    {/if}

    <h2>Waar moet u aan voldoen?</h2>
    <p>U moet voldoen aan de verplichtingen in de wet- en regelgeving die van toepassing zijn op de subsidie en aan al
        hetgeen in deze beschikking is opgenomen.</p>

    <p>Wet- en regelgeving
        De volgende regelgeving is in ieder geval van toepassing op de subsidie:<br/>
        <ul>
            <li>Kaderwet VWS-subsidies</li>
            <li>Algemene wet bestuursrecht, in het bijzonder titel 4.2 Subsidies;</li>
            <li>Subsidieregeling borstprothesen transvrouwen</li>
        </ul>
    </p>

    <p>De regelgeving kunt u raadplegen via <a href="https://wetten.overheid.nl" target="_blank">wetten.overheid.nl</a>.</p>

    <p>
        U bent zelf verantwoordelijk voor de naleving hiervan en de eventuele gevolgen
        bij niet-naleving.
    </p>

    <p>Zonder volledig te zijn breng ik in het bijzonder de volgende bepalingen uit de
        wet- en regelgeving onder uw aandacht.</p>

    <p>
        <b>Operatief plaatsen van borstprothesen</b><br/>
        Deze subsidie wordt verstrekt ten behoeve van het operatief plaatsen van borstprothesen en de medisch
        noodzakelijke kosten die samenhangen met deze operatie. De subsidie mag niet worden gebruikt voor andere
        (operatieve) ingrepen ten behoeve van een borstvergroting.
    </p>

    <p>
        <b>Meldingsplicht</b><br/>
        Indien zich na indiening van de aanvraag omstandigheden voordoen die van belang kunnen zijn voor de beslissing
        tot vaststelling van de subsidie, doet u daarvan zo spoedig mogelijk schriftelijk mededeling aan de Minister zo
        mogelijk onder overlegging van de relevante stukken.
    </p>

    <p>
        Uw melding, voorzien van toelichting en relevante stukken, doet u schriftelijk bij de Dienst Uitvoering Subsidies
        aan Instellingen (DUS-I) onder vermelding van het zaaknummer {$content->reference}. Als u twijfelt of u iets moet
        melden, verzoek ik u contact op te nemen met uw contactpersoon.
    </p>

    <h2>Wat als u zich niet aan de voorschriften houdt?</h2>
    <p>
        Het niet voldoen aan de verplichtingen die aan de subsidie verbonden zijn of het
        niet (geheel) verrichten van de activiteiten kan tot gevolg hebben dat ik de
        subsidie geheel of gedeeltelijk terugvorder.

    </p>
    <p>
        Ik wijs u er verder op dat een registratie van (ernstige) onregelmatigheden bij
        subsidies wordt bijgehouden met het oog op het tegengaan van misbruik van
        subsidie.
    </p>

    <h2>Wanneer ontvangt u de financiële ondersteuning?</h2>
    <p>
        U ontvangt een voorschot van 100% van het subsidiebedrag.<br>
        Ik streef ernaar dit binnen 10 werkdagen naar u over te maken onder vermelding van het
        referentienummer {$content->reference}.
    </p>

    <h2>Wanneer wordt de subsidie vastgesteld?</h2>
    <p>
        Op basis van artikel 10 van de subsidieregeling neemt de minister binnen 22 weken na afloop van de datum waarop
        de operatie waarvoor de subsidie is verleend, moet zijn verricht, ambtshalve een besluit over de vaststelling
        van de subsidie. Er vindt een steekproefsgewijze controle plaats. Indien u voor deze steekproef geselecteerd
        wordt, zal er aan u gevraagd worden om een factuur van de operatie aan te leveren. Daarom is het advies om uw
        factuur goed te bewaren.
    </p>

{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de minister van Volksgezondheid, Welzijn en Sport,<br/>
        namens deze,<br/>
        de directeur Curatieve Zorg,<br/>
        voor deze,<br/>
        het afdelingshoofd Dienst Uitvoering Subsidies aan Instellingen,<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(\'vws_dusi_signature.jpg\')|dataStream}" />
        <br/>
        P.A. van Hecking Colenbrander
    </p>
{/block}

{block sidebar}
    {include parent}

    <h4>Relatienummer</h4>
    <p>{$content->stage2->businessPartnerNumber}</p>

    <h4>Verplichtingennummer</h4>
    <p>{$content->stage2->liabilitiesNumber}</p>
{/block}
' WHERE id = '1983fa28-cfc6-4c0f-9bc3-cba9e0909456';
