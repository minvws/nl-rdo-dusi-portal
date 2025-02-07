-- BTV
-- TRANSITION_STAGE_2_TO_1_MESSAGE - cffe3600-77a9-43b2-9882-7b7f56c4d0ad

UPDATE public.subsidy_stage_transition_messages
SET content_html = '{layout ''letter_view_layout.latte''}

{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling ''{$content->subsidyTitle}'' met referentienummer {$content->reference}.
    </p>

    <p>Uw aanvraag bevat helaas nog onvoldoende gegevens om verder in behandeling te nemen. Met deze brief verzoek ik u om uw aanvraag aan te vullen.</p>

    <h2>Wat moet u aanvullen?</h2>
    <p>
        Ik verzoek u om uw aanvraag aan te vullen met:<br/>
        {$content->stage2->firstAssessmentRequestedComplementNote|breakLines}
    </p>

    <h2>Termijn</h2>
    <p>
        Ik ontvang de informatie graag binnen 2 weken na dagtekening van deze brief, dus uiterlijk {$content->createdAt->addDays(14)|date:"d-m-Y"}.
    </p>
    <p>
        U kunt de ontbrekende informatie aan uw aanvraag toevoegen door het door u ingevulde aanvraagformulier te heropenen in Mijn DUS-I.
    </p>
    <p>
        Als de gevraagde gegevens niet binnen 2 weken door mij zijn ontvangen, of onvoldoende zijn voor verdere beoordeling, dan kan uw aanvraag niet verder worden behandeld.
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
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        P.A. van Hecking Colenbrander
    </p>
{/block}

{block objectionFooter}{/block}
',
    content_pdf = '{layout ''letter_layout.latte''}

{block concern}
    Betreft: Verzoek om aanvulling aanvraag ''{$content->subsidyTitle}''
{/block}

{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling ''{$content->subsidyTitle}'' met referentienummer {$content->reference}.
    </p>

    <p>Uw aanvraag bevat helaas nog onvoldoende gegevens om verder in behandeling te nemen. Met deze brief verzoek ik u om uw aanvraag aan te vullen.</p>

    <h2>Wat moet u aanvullen?</h2>
    <p>
        Ik verzoek u om uw aanvraag aan te vullen met:<br/>
        {$content->stage2->firstAssessmentRequestedComplementNote|breakLines}
    </p>

    <h2>Termijn</h2>
    <p>
        Ik ontvang de informatie graag binnen 2 weken na dagtekening van deze brief, dus uiterlijk {$content->createdAt->addDays(14)|date:"d-m-Y"}.
    </p>
    <p>
        U kunt de ontbrekende informatie aan uw aanvraag toevoegen door het door u ingevulde aanvraagformulier te heropenen in Mijn DUS-I.
    </p>
    <p>
        Als de gevraagde gegevens niet binnen 2 weken door mij zijn ontvangen, of onvoldoende zijn voor verdere beoordeling, dan kan uw aanvraag niet verder worden behandeld.
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
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        P.A. van Hecking Colenbrander
    </p>
{/block}

{block objectionFooter}{/block}
',
    updated_at = 'now()'
WHERE id = 'cffe3600-77a9-43b2-9882-7b7f56c4d0ad';

-- TRANSITION_STAGE_4_TO_REJECTED_MESSAGE - c3b32e69-e093-4f0f-9318-7cc771114f2d

UPDATE public.subsidy_stage_transition_messages
SET content_html = '{layout ''letter_view_layout.latte''}

{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling ''{$content->subsidyTitle}'' met referentienummer {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>

    <h2>Besluit</h2>
    <p>Uw aanvraag moet ik helaas afwijzen. De reden(en) licht ik hieronder toe.</p>

    <h2>Motivering bij het besluit</h2>

    <p>U heeft een aanvraag voor de subsidieregeling borstprothesen transvrouwen ingediend, die verstrekt wordt op grond
        van de subsidieregeling borstprothesen transvrouwen (hierna: de Beleidsregel). Het doel van de Beleidsregel is
        dat man-vrouw transgenders met genderdysforie, die zich in een medisch transitietraject bevinden, door een
        vergroting van de borsten een vrouwelijk(er) profiel kunnen krijgen.</p>

    <p>In artikel 4 van de Beleidsregel is opgenomen dat een subsidie uitsluitend wordt toegekend aan transvrouwen die:</p>
    <ul>
        <li>Ingezetene zijn in Nederland;</li>
        <li>Ouder zijn dan 18 jaar;</li>
        <li>Op voorschrift van een BIG-geregistreerde arts:<br/>
            <ul>
                <li>minimaal een jaar voorafgaand aan de subsidieaanvraag genderbevestigende behandeling hebben
                    ondergaan;
                    of
                </li>
                <li>op medische gronden geen genderbevestigende hormoonbehandelingen kunnen ondergaan; of</li>
                <li>om medische redenen binnen de tijdspanne van een jaar zijn gestopt met de hoormoontherapie;</li>
            </ul>
        </li>
        <li>De operatie nog niet ondergaan hebben;</li>
        <li>Geen aanspraak kunnen maken op vergoeding van het operatief plaatsen van borstprothesen op grond van artikel
            2.1, onderdeel c, van de Regeling zorgverzekering.
        </li>
    </ul>

    {if $content->stage2->firstAssessmentRejectedNote}
        <p>
            Uw aanvraag is getoetst aan de bovengenoemde voorwaarden en wordt afgewezen vanwege de volgende
            reden(en):
        </p>
        <p>{$content->stage2->firstAssessmentRejectedNote|breakLines}</p>
    {/if}
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
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        P.A. van Hecking Colenbrander
    </p>
{/block}
', content_pdf = '{layout ''letter_layout.latte''}

{block concern}
    Betreft: Afwijzing aanvraag ''{$content->subsidyTitle}''
{/block}

{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling ''{$content->subsidyTitle}'' met referentienummer {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>

    <h2>Besluit</h2>
    <p>Uw aanvraag moet ik helaas afwijzen. De reden(en) licht ik hieronder toe.</p>

    <h2>Motivering bij het besluit</h2>

    <p>U heeft een aanvraag voor de subsidieregeling borstprothesen transvrouwen ingediend, die verstrekt wordt op grond
        van de subsidieregeling borstprothesen transvrouwen (hierna: de Beleidsregel). Het doel van de Beleidsregel is
        dat man-vrouw transgenders met genderdysforie, die zich in een medisch transitietraject bevinden, door een
        vergroting van de borsten een vrouwelijk(er) profiel kunnen krijgen.</p>

    <p>In artikel 4 van de Beleidsregel is opgenomen dat een subsidie uitsluitend wordt toegekend aan transvrouwen die:</p>
    <ul>
        <li>Ingezetene zijn in Nederland;</li>
        <li>Ouder zijn dan 18 jaar;</li>
        <li>Op voorschrift van een BIG-geregistreerde arts:<br/>
            <ul>
                <li>minimaal een jaar voorafgaand aan de subsidieaanvraag genderbevestigende behandeling hebben
                    ondergaan;
                    of
                </li>
                <li>op medische gronden geen genderbevestigende hormoonbehandelingen kunnen ondergaan; of</li>
                <li>om medische redenen binnen de tijdspanne van een jaar zijn gestopt met de hoormoontherapie;</li>
            </ul>
        </li>
        <li>De operatie nog niet ondergaan hebben;</li>
        <li>Geen aanspraak kunnen maken op vergoeding van het operatief plaatsen van borstprothesen op grond van artikel
            2.1, onderdeel c, van de Regeling zorgverzekering.
        </li>
    </ul>

    {if $content->stage2->firstAssessmentRejectedNote}
        <p>
            Uw aanvraag is getoetst aan de bovengenoemde voorwaarden en wordt afgewezen vanwege de volgende
            reden(en):
        </p>
        <p>{$content->stage2->firstAssessmentRejectedNote|breakLines}</p>
    {/if}
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
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        P.A. van Hecking Colenbrander
    </p>
{/block}
',
    updated_at = 'now()'
WHERE id = 'c3b32e69-e093-4f0f-9318-7cc771114f2d';

-- TRANSITION_STAGE_4_TO_5_ALLOCATED_MESSAGE - 532d7372-a029-4190-bf8f-c8417ce9acb4

UPDATE public.subsidy_stage_transition_messages
SET content_html = '{layout ''letter_view_layout.latte''}

{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling ''{$content->subsidyTitle}'' met referentienummer {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>

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

    {if $content->stage2->firstAssessmentApprovedNote}
        <h2>Motivering bij het besluit</h2>
        <p>{$content->stage2->firstAssessmentApprovedNote|breakLines}</p>
    {/if}

    <h2>Waar moet u aan voldoen?</h2>
    <p>U moet voldoen aan de verplichtingen in de wet- en regelgeving die van toepassing zijn op de subsidie en aan al
        hetgeen in deze beschikking is opgenomen.</p>

    <p><u>Wet- en regelgeving</u><br/>
        De volgende regelgeving is in ieder geval van toepassing op de subsidie:
    </p>
    <ul>
        <li>Kaderwet VWS-subsidies</li>
        <li>Algemene wet bestuursrecht, in het bijzonder titel 4.2 Subsidies;</li>
        <li>Subsidieregeling borstprothesen transvrouwen</li>
    </ul>

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
        <u>Meldingsplicht</u><br/>
        Indien zich na indiening van de aanvraag omstandigheden voordoen die van belang kunnen zijn voor de beslissing
        tot vaststelling van de subsidie, doet u daarvan zo spoedig mogelijk schriftelijk mededeling aan de Minister zo
        mogelijk onder overlegging van de relevante stukken.
    </p>

    <p>
        Uw melding, voorzien van toelichting en relevante stukken, doet u schriftelijk bij de Dienst Uitvoering Subsidies
        aan Instellingen (DUS-I) onder vermelding van het zaaknummer {$content->reference}. Als u twijfelt of u iets moet
        melden, verzoek ik u contact op te nemen met uw contactpersoon.
    </p>

    <p><u>Wat als u zich niet aan de voorschriften houdt?</u>
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
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        P.A. van Hecking Colenbrander
    </p>
{/block}
',
    content_pdf = '{layout ''letter_layout.latte''}

{block concern}
    Betreft: Verlening aanvraag ''{$content->subsidyTitle}''
{/block}

{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling ''{$content->subsidyTitle}'' met referentienummer {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>

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

    {if $content->stage2->firstAssessmentApprovedNote}
        <h2>Motivering bij het besluit</h2>
        <p>{$content->stage2->firstAssessmentApprovedNote|breakLines}</p>
    {/if}

    <h2>Waar moet u aan voldoen?</h2>
    <p>U moet voldoen aan de verplichtingen in de wet- en regelgeving die van toepassing zijn op de subsidie en aan al
        hetgeen in deze beschikking is opgenomen.</p>

    <p><u>Wet- en regelgeving</u><br/>
        De volgende regelgeving is in ieder geval van toepassing op de subsidie:
    </p>
    <ul>
        <li>Kaderwet VWS-subsidies</li>
        <li>Algemene wet bestuursrecht, in het bijzonder titel 4.2 Subsidies;</li>
        <li>Subsidieregeling borstprothesen transvrouwen</li>
    </ul>

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
        <u>Meldingsplicht</u><br/>
        Indien zich na indiening van de aanvraag omstandigheden voordoen die van belang kunnen zijn voor de beslissing
        tot vaststelling van de subsidie, doet u daarvan zo spoedig mogelijk schriftelijk mededeling aan de Minister zo
        mogelijk onder overlegging van de relevante stukken.
    </p>

    <p>
        Uw melding, voorzien van toelichting en relevante stukken, doet u schriftelijk bij de Dienst Uitvoering Subsidies
        aan Instellingen (DUS-I) onder vermelding van het zaaknummer {$content->reference}. Als u twijfelt of u iets moet
        melden, verzoek ik u contact op te nemen met uw contactpersoon.
    </p>

    <p><u>Wat als u zich niet aan de voorschriften houdt?</u>
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
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        P.A. van Hecking Colenbrander
    </p>
{/block}

{block sidebar}
    {include parent}

    <h4>Relatienummer</h4>
    <p>{$content->stage2->businessPartnerNumber}</p>
{/block}
',
    updated_at = 'now()'
WHERE id = '532d7372-a029-4190-bf8f-c8417ce9acb4';

-- TRANSITION_STAGE_8_TO_APPROVED_MESSAGE - d8c2a8d1-e512-40a1-94f8-6535cc85289c

UPDATE public.subsidy_stage_transition_messages
SET content_html = '{layout ''letter_view_layout.latte''}

{block content}
    <p>Beste lezer,</p>
    <p>Met mijn brief van {$content->stage4->closedAt|date:"d-m-Y"} heb ik u een subsidie verleend van
        {$content->stage2->amount} voor de regeling ''{$content->subsidyTitle}''. Uit de aan mij ter beschikking
        gestelde gegevens blijkt dat u inmiddels voldoet aan de vereisten voor deze regeling.
    </p>

    <h2>Ambtshalve subsidievaststelling</h2>
    <p>Hierbij deel ik u mede dat ik uw verleende subsidie voor de borstprothesen transvrouwen met
        kenmerk {$content->reference}, ambtshalve vaststel op {$content->stage2->amount}.</p>

    <p>De subsidie is vastgesteld op grond van:<br/>
        <ul>
            <li>Kaderwet VWS-subsidies</li>
            <li>Algemene wet bestuursrecht, in het bijzonder titel 4.2 Subsidies;</li>
            <li>Subsidieregeling borstprothesen transvrouwen</li>
        </ul>
    </p>

    <h2>Hoe wordt de subsidie afgehandeld?</h2>
    <p>
        De subsidie is reeds volledig aan u uitbetaald. Er zal geen verdere betaling of terugvordering plaatsvinden.
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
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        P.A. van Hecking Colenbrander
    </p>
{/block}
',
    content_pdf = '{layout ''letter_layout.latte''}

{block concern}
    Betreft: Ambtshalve vaststelling subsidie ''{$content->subsidyTitle}''
{/block}

{block content}
    <p>Beste lezer,</p>
    <p>Met mijn brief van {$content->stage4->closedAt|date:"d-m-Y"} heb ik u een subsidie verleend van
        {$content->stage2->amount} voor de regeling ''{$content->subsidyTitle}''. Uit de aan mij ter beschikking
        gestelde gegevens blijkt dat u inmiddels voldoet aan de vereisten voor deze regeling.
    </p>

    <h2>Ambtshalve subsidievaststelling</h2>
    <p>Hierbij deel ik u mede dat ik uw verleende subsidie voor de borstprothesen transvrouwen met
        kenmerk {$content->reference}, ambtshalve vaststel op {$content->stage2->amount}.</p>

    <p>De subsidie is vastgesteld op grond van:<br/>
        <ul>
            <li>Kaderwet VWS-subsidies</li>
            <li>Algemene wet bestuursrecht, in het bijzonder titel 4.2 Subsidies;</li>
            <li>Subsidieregeling borstprothesen transvrouwen</li>
        </ul>
    </p>

    <h2>Hoe wordt de subsidie afgehandeld?</h2>
    <p>
        De subsidie is reeds volledig aan u uitbetaald. Er zal geen verdere betaling of terugvordering plaatsvinden.
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
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        P.A. van Hecking Colenbrander
    </p>
{/block}

{block sidebar}
    {include parent}

    <h4>Relatienummer</h4>
    <p>{$content->stage2->businessPartnerNumber}</p>

{/block}
',
    updated_at = 'now()'
WHERE id = 'd8c2a8d1-e512-40a1-94f8-6535cc85289c';


-- TRANSITION_STAGE_8_TO_RECLAIM_MESSAGE - d9917011-3baf-4a5f-8b1f-0e8e2b62d0a4

UPDATE public.subsidy_stage_transition_messages
SET content_html = '{layout ''letter_view_layout.latte''}

{block content}
    <p>Beste lezer,</p>

    <p>Bij brief van {$content->lastAllocatedAt|date:''d-m-Y''}, met kenmerk {$content->reference}, heb ik aan u een subsidie verleend van
        {$content->stage2->amount} voor de subsidieregeling borstprothesen transvrouwen (hierna: Subsidieregeling). In mijn
        administratie is uw aanvraag bekend onder nummer: {$content->reference}.</p>

    {if $content->stage5->motivation}
        <p>{$content->stage5->motivation}</p>
        {else}
            {if $content->stage6->motivation}
                <p>{$content->stage6->motivation}</p>
            {/if}
    {/if}

    <h2>Besluit</h2>

    {var $newAssignationAmount = $content->stage5->newAssignationAmount ? $content->stage5->newAssignationAmount : $content->stage6->newAssignationAmount}
    <p>Ik stel de subsidie vast op een lager bedrag dan aan u is verleend, namelijk € {formatCurrency($newAssignationAmount)}.</p>

    <p>Mocht u in de toekomst alsnog gebruiken willen maken van de Subsidieregeling borstprothesen transvrouwen, dan dient u
        een volledig nieuwe aanvraag in te dienen, conform de vereisten die in artikel 7 van de regeling zijn opgenomen. U
        kunt zich niet beroepen op al eerder ingediende documenten.</p>

    <h2>Hoe wordt de subsidie afgehandeld?</h2>

    <p>De subsidie is geheel als voorschot aan u uitbetaald.</p>

    <p>Het verschil tussen het vastgestelde subsidiebedrag en het ontvangen voorschot bedraagt {$content->stage2->amount}. Ik vorder dit
        verschil van u terug op grond van artikel 4:57 van de Algemene wet bestuursrecht.</p>

    {var $reclaimAmount = $content->stage5->reclaimAmount ? $content->stage5->reclaimAmount : $content->stage6->reclaimAmount}
    <p>Ik verzoek u het bedrag van € {formatCurrency($reclaimAmount)} binnen zes weken na de datum van deze beschikking over te
        maken op bankrekeningnummer: NL55INGB0705003566 ten name van VWS — Financieel Dienstencentrum onder vermelding van
        het vorderingsnummer {$content->stage7->reclaimNumber} en subsidienummer {$content->reference}.</p>
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
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        P.A. van Hecking Colenbrander
    </p>
{/block}
',
    content_pdf = '{layout ''letter_layout.latte''}

{block concern}
    Betreft: Vordering subsidie ''{$content->subsidyTitle}''
{/block}

{block content}
    <p>Beste lezer,</p>

    <p>Bij brief van {$content->lastAllocatedAt|date:''d-m-Y''}, met kenmerk {$content->reference}, heb ik aan u een subsidie verleend van
        {$content->stage2->amount} voor de subsidieregeling borstprothesen transvrouwen (hierna: Subsidieregeling). In mijn
        administratie is uw aanvraag bekend onder nummer: {$content->reference}.</p>

    {if $content->stage5->motivation}
        <p>{$content->stage5->motivation}</p>
        {else}
            {if $content->stage6->motivation}
                <p>{$content->stage6->motivation}</p>
            {/if}
    {/if}

    <h2>Besluit</h2>

    {var $newAssignationAmount = $content->stage5->newAssignationAmount ? $content->stage5->newAssignationAmount : $content->stage6->newAssignationAmount}
    <p>Ik stel de subsidie vast op een lager bedrag dan aan u is verleend, namelijk € {formatCurrency($newAssignationAmount)}.</p>

    <p>Mocht u in de toekomst alsnog gebruiken willen maken van de Subsidieregeling borstprothesen transvrouwen, dan dient u
        een volledig nieuwe aanvraag in te dienen, conform de vereisten die in artikel 7 van de regeling zijn opgenomen. U
        kunt zich niet beroepen op al eerder ingediende documenten.</p>

    <h2>Hoe wordt de subsidie afgehandeld?</h2>

    <p>De subsidie is geheel als voorschot aan u uitbetaald.</p>

    <p>Het verschil tussen het vastgestelde subsidiebedrag en het ontvangen voorschot bedraagt {$content->stage2->amount}. Ik vorder dit
        verschil van u terug op grond van artikel 4:57 van de Algemene wet bestuursrecht.</p>

    {var $reclaimAmount = $content->stage5->reclaimAmount ? $content->stage5->reclaimAmount : $content->stage6->reclaimAmount}
    <p>Ik verzoek u het bedrag van € {formatCurrency($reclaimAmount)} binnen zes weken na de datum van deze beschikking over te
        maken op bankrekeningnummer: NL55INGB0705003566 ten name van VWS — Financieel Dienstencentrum onder vermelding van
        het vorderingsnummer {$content->stage7->reclaimNumber} en subsidienummer {$content->reference}.</p>
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
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        P.A. van Hecking Colenbrander
    </p>
{/block}

{block sidebar}
    {include parent}

    {if ($content->stage2->businessPartnerNumber)}
    <h4>Relatienummer</h4>
    <p>{$content->stage2->businessPartnerNumber}</p>
    {/if}

    {if ($content->stage7->reclaimNumber)}
    <h4>Vorderingsnummer</h4>
    <p>{$content->stage7->reclaimNumber}</p>
    {/if}

    <p><i>Correspondentie uitsluitend richten aan het retouradres met vermelding van de datum het kenmerk van deze brief en met
            het onderwerp op de enveloppe.</i></p>
{/block}
',
    updated_at = 'now()'
WHERE id = 'd9917011-3baf-4a5f-8b1f-0e8e2b62d0a4';
