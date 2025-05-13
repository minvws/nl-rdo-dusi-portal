-- cffe3600-77a9-43b2-9882-7b7f56c4d0ad -- requestForChanges

UPDATE public.subsidy_stage_transition_messages
SET content_html = '{layout ''letter_view_layout.latte''}

{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling ''{$content->subsidyTitle}'' met kenmerk {$content->reference}.
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

{block questions}
    <h2>Vragen?</h2>
    <p>Neem contact op met DUS-I via telefoon: <a href="tel:+31703405566">070 3405566</a> of gebruik het contactformulier op <a href="https://www.dus-i.nl/btv">www.dus-i.nl/btv</a>.
     Vermeld hierbij het kenmerk <em>{$content->reference}</em>.</p>
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        namens de minister van Volksgezondheid, Welzijn en Sport,<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        Aryan van Driel<br/>
        Algemeen directeur<br/>
        Dienst Uitvoering Subsidies aan Instellingen
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
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling ''{$content->subsidyTitle}'' met kenmerk {$content->reference}.
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

{block questions}
    <h2>Vragen?</h2>
    <p>Neem contact op met DUS-I via telefoon: <a href="tel:+31703405566">070 3405566</a> of gebruik het contactformulier op <a href="https://www.dus-i.nl/btv">www.dus-i.nl/btv</a>.
     Vermeld hierbij het kenmerk <em>{$content->reference}</em>.</p>
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        namens de minister van Volksgezondheid, Welzijn en Sport,<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        Aryan van Driel<br/>
        Algemeen directeur<br/>
        Dienst Uitvoering Subsidies aan Instellingen
    </p>
{/block}

{block objectionFooter}{/block}
',
    updated_at = 'now()'
WHERE id = 'cffe3600-77a9-43b2-9882-7b7f56c4d0ad';


-- c3b32e69-e093-4f0f-9318-7cc771114f2d -- rejected

UPDATE public.subsidy_stage_transition_messages
SET content_html = '{layout ''letter_view_layout.latte''}

{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling ''{$content->subsidyTitle}'' met kenmerk {$content->reference}.
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

{block questions}
    <h2>Vragen?</h2>
    <p>Neem contact op met DUS-I via telefoon: <a href="tel:+31703405566">070 3405566</a> of gebruik het contactformulier op <a href="https://www.dus-i.nl/btv">www.dus-i.nl/btv</a>.
     Vermeld hierbij het kenmerk <em>{$content->reference}</em>.</p>
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        namens de minister van Volksgezondheid, Welzijn en Sport,<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        Aryan van Driel<br/>
        Algemeen directeur<br/>
        Dienst Uitvoering Subsidies aan Instellingen
    </p>
{/block}
',
    content_pdf = '{layout ''letter_layout.latte''}

{block concern}
    Betreft: Afwijzing aanvraag ''{$content->subsidyTitle}''
{/block}

{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling ''{$content->subsidyTitle}'' met kenmerk {$content->reference}.
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

{block questions}
    <h2>Vragen?</h2>
    <p>Neem contact op met DUS-I via telefoon: <a href="tel:+31703405566">070 3405566</a> of gebruik het contactformulier op <a href="https://www.dus-i.nl/btv">www.dus-i.nl/btv</a>.
     Vermeld hierbij het kenmerk <em>{$content->reference}</em>.</p>
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        namens de minister van Volksgezondheid, Welzijn en Sport,<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        Aryan van Driel<br/>
        Algemeen directeur<br/>
        Dienst Uitvoering Subsidies aan Instellingen
    </p>
{/block}
',
    updated_at = 'now()'
WHERE id = 'c3b32e69-e093-4f0f-9318-7cc771114f2d';

-- 532d7372-a029-4190-bf8f-c8417ce9acb4 -- allocated

UPDATE public.subsidy_stage_transition_messages
SET content_html = '{layout ''letter_view_layout.latte''}

{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling ''{$content->subsidyTitle}'' met kenmerk {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>

    <h2>Besluit</h2>
    <p>
        Uw aanvraag is goedgekeurd. Dit betekent dat u een subsidie ontvangt van
        {$content->stage2->amount} voor het uitvoeren van de behandeling binnen 1 jaar na {$content->submittedAt|date:"d-m-Y"}.
    </p>

    {if $content->stage2->firstAssessmentApprovedNote}
        <h2>Motivering bij het besluit</h2>
        <p>{$content->stage2->firstAssessmentApprovedNote|breakLines}</p>
    {/if}

    <h2>Waar is de subsidie voor bedoeld?</h2>
    <p>U ontvangt de subsidie om een borstconstructie uit te laten voeren. De subsidie is bedoeld voor het
       operatief laten plaatsen van borstprothesen en de medisch noodzakelijke kosten die nodig zijn voor
       deze operatie.</p>

    <p>Let op! U mag de subsidie niet gebruiken voor autologe vettransplantatie (AFT), ook wel ‘lipofilling’
       genoemd.</p>

    <h2>Wanneer ontvangt u het geld?</h2>
    <p>
        U ontvangt binnen 2 weken het hele bedrag van {$content->stage2->amount} als voorschot op het door u
        doorgegeven rekeningnummer. Wij gebruiken daarbij kenmerk {$content->reference}.
    </p>

    <h2>Wat verwachten wij van u?</h2>
    <p>U moet wijzigingen zo snel mogelijk bij ons melden. Bijvoorbeeld:
    </p>
    <ul>
        <li>De behandeling gaat niet door. In dit geval moet u de ontvangen subsidie terugbetalen.</li>
        <li>De behandeling wordt uitgesteld. Vindt de behandeling niet binnen 1 jaar na aanvraag van de
            subsidie plaats? Dan kunt u binnen dat jaar uitstel aanvragen. Doet u dit niet? Dan moet u de
            ontvangen subsidie terugbetalen. Wel kunt u daarna een nieuwe aanvraag doen.</li>
        <li>Er verandert iets aan uw situatie wat gevolgen kan hebben voor de behandeling en/of de
            subsidie. Ook in dit geval moet u dit aan ons melden, zodat we samen kunnen kijken naar uw
            situatie.</li>
    </ul>

    <p>Op tijd melden is belangrijk omdat wijzigingen gevolgen kunnen hebben voor uw subsidie en de vraag
       of u de subsidie mag houden. Door te melden ontvangt u de juiste informatie over uw subsidie.</p>

    <p>
        Vermeld in de melding uw kenmerk {$content->reference} en leg duidelijk uit wat er is veranderd aan
        uw situatie. Heeft u documenten die relevant zijn voor uw melding? Stuur deze dan mee. Gebruik het
        contactformulier op <a href="https://www.dus-i.nl/btv">www.dus-i.nl/btv</a>.
    </p>

    <p>Twijfelt u of u iets moet melden? Neem dan contact met ons op via <a href="tel:+31703405566">070 3405566</a>.</p>

    <h2>Controle</h2>
    <p>
        De subsidie wordt gecontroleerd. Dit gebeurt dan na afloop van de subsidietermijn. DUS-I kan u
        benaderen om aan te tonen dat de behandeling heeft plaatsgevonden. Bij deze controle moet u de
        factuur van de borstoperatie opsturen. Daarom moet u de factuur goed bewaren. Als er vragen zijn
        over de factuur dan kan DUS-I ook vragen om bijvoorbeeld een bankafschrift of een verklaring van de
        kliniek of het ziekenhuis op te sturen.
    </p>

    <h2>Vaststelling van de subsidie</h2>
    <p>Binnen 22 weken na de datum waarop de operatie moet zijn verricht, ontvangt u het besluit waarin staat
       of u de subsidie mag houden. Dit wordt de vaststelling van de subsidie genoemd. De uiterlijke datum
       waarop u hoort of u de subsidie mag houden is ook zichtbaar in Mijn DUS-I, als uiterste
       behandeldatum.
    </p>

    <h2>Wat als u zich niet aan de regels houdt?</h2>
    <p>
        Als u niet voldoet aan de regels en voorwaarden van de subsidie of als u de behandeling niet laat
        uitvoeren, dan moet u de subsidie terugbetalen.
    </p>
    <p>
        Wij houden een registratie bij van (ernstige) onregelmatigheden om het misbruik van subsidie tegen te
        gaan.
    </p>

    <h2>Belangrijke informatie</h2>
    <p>
        De volgende regelgeving is in ieder geval van toepassing op de subsidie:
    </p>
    <ul>
        <li>Kaderwet VWS-subsidies</li>
        <li>Algemene wet bestuursrecht, in het bijzonder titel 4.2 Subsidies;</li>
        <li>Subsidieregeling borstprothesen transvrouwen</li>
    </ul>
    <p>Deze kunt u bekijken via <a href="https://wetten.overheid.nl">wetten.overheid.nl</a>.</p>
{/block}

{block questions}
    <h2>Vragen?</h2>
    <p>Neem contact op met DUS-I via telefoon: <a href="tel:+31703405566">070 3405566</a> of gebruik het contactformulier op <a href="https://www.dus-i.nl/btv">www.dus-i.nl/btv</a>.
     Vermeld hierbij het kenmerk <em>{$content->reference}</em>.</p>
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        namens de minister van Volksgezondheid, Welzijn en Sport,<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        Aryan van Driel<br/>
        Algemeen directeur<br/>
        Dienst Uitvoering Subsidies aan Instellingen
    </p>
{/block}
',
    content_pdf = '{layout ''letter_layout.latte''}

{block concern}
    Betreft: Besluit toekenning subsidie borstprothesen transvrouwen
{/block}

{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling ''{$content->subsidyTitle}'' met kenmerk {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>

    <h2>Besluit</h2>
    <p>
        Uw aanvraag is goedgekeurd. Dit betekent dat u een subsidie ontvangt van
        {$content->stage2->amount} voor het uitvoeren van de behandeling binnen 1 jaar na {$content->submittedAt|date:"d-m-Y"}.
    </p>

    {if $content->stage2->firstAssessmentApprovedNote}
        <h2>Motivering bij het besluit</h2>
        <p>{$content->stage2->firstAssessmentApprovedNote|breakLines}</p>
    {/if}

    <h2>Waar is de subsidie voor bedoeld?</h2>
    <p>U ontvangt de subsidie om een borstconstructie uit te laten voeren. De subsidie is bedoeld voor het
       operatief laten plaatsen van borstprothesen en de medisch noodzakelijke kosten die nodig zijn voor
       deze operatie.</p>

    <p>Let op! U mag de subsidie niet gebruiken voor autologe vettransplantatie (AFT), ook wel ‘lipofilling’
       genoemd.</p>

    <h2>Wanneer ontvangt u het geld?</h2>
    <p>
        U ontvangt binnen 2 weken het hele bedrag van {$content->stage2->amount} als voorschot op het door u
        doorgegeven rekeningnummer. Wij gebruiken daarbij kenmerk {$content->reference}.
    </p>

    <h2>Wat verwachten wij van u?</h2>
    <p>U moet wijzigingen zo snel mogelijk bij ons melden. Bijvoorbeeld:
    </p>
    <ul>
        <li>De behandeling gaat niet door. In dit geval moet u de ontvangen subsidie terugbetalen.</li>
        <li>De behandeling wordt uitgesteld. Vindt de behandeling niet binnen 1 jaar na aanvraag van de
            subsidie plaats? Dan kunt u binnen dat jaar uitstel aanvragen. Doet u dit niet? Dan moet u de
            ontvangen subsidie terugbetalen. Wel kunt u daarna een nieuwe aanvraag doen.</li>
        <li>Er verandert iets aan uw situatie wat gevolgen kan hebben voor de behandeling en/of de
            subsidie. Ook in dit geval moet u dit aan ons melden, zodat we samen kunnen kijken naar uw
            situatie.</li>
    </ul>

    <p>Op tijd melden is belangrijk omdat wijzigingen gevolgen kunnen hebben voor uw subsidie en de vraag
       of u de subsidie mag houden. Door te melden ontvangt u de juiste informatie over uw subsidie.</p>

    <p>
        Vermeld in de melding uw kenmerk {$content->reference} en leg duidelijk uit wat er is veranderd aan
        uw situatie. Heeft u documenten die relevant zijn voor uw melding? Stuur deze dan mee. Gebruik het
        contactformulier op <a href="https://www.dus-i.nl/btv">www.dus-i.nl/btv</a>.
    </p>

    <p>Twijfelt u of u iets moet melden? Neem dan contact met ons op via <a href="tel:+31703405566">070 3405566</a>.</p>

    <h2>Controle</h2>
    <p>
        De subsidie wordt gecontroleerd. Dit gebeurt dan na afloop van de subsidietermijn. DUS-I kan u
        benaderen om aan te tonen dat de behandeling heeft plaatsgevonden. Bij deze controle moet u de
        factuur van de borstoperatie opsturen. Daarom moet u de factuur goed bewaren. Als er vragen zijn
        over de factuur dan kan DUS-I ook vragen om bijvoorbeeld een bankafschrift of een verklaring van de
        kliniek of het ziekenhuis op te sturen.
    </p>

    <h2>Vaststelling van de subsidie</h2>
    <p>Binnen 22 weken na de datum waarop de operatie moet zijn verricht, ontvangt u het besluit waarin staat
       of u de subsidie mag houden. Dit wordt de vaststelling van de subsidie genoemd. De uiterlijke datum
       waarop u hoort of u de subsidie mag houden is ook zichtbaar in Mijn DUS-I, als uiterste
       behandeldatum.
    </p>

    <h2>Wat als u zich niet aan de regels houdt?</h2>
    <p>
        Als u niet voldoet aan de regels en voorwaarden van de subsidie of als u de behandeling niet laat
        uitvoeren, dan moet u de subsidie terugbetalen.
    </p>
    <p>
        Wij houden een registratie bij van (ernstige) onregelmatigheden om het misbruik van subsidie tegen te
        gaan.
    </p>

    <h2>Belangrijke informatie</h2>
    <p>
        De volgende regelgeving is in ieder geval van toepassing op de subsidie:
    </p>
    <ul>
        <li>Kaderwet VWS-subsidies</li>
        <li>Algemene wet bestuursrecht, in het bijzonder titel 4.2 Subsidies;</li>
        <li>Subsidieregeling borstprothesen transvrouwen</li>
    </ul>
    <p>Deze kunt u bekijken via <a href="https://wetten.overheid.nl">wetten.overheid.nl</a>.</p>
{/block}

{block questions}
    <h2>Vragen?</h2>
    <p>Neem contact op met DUS-I via telefoon: <a href="tel:+31703405566">070 3405566</a> of gebruik het contactformulier op <a href="https://www.dus-i.nl/btv">www.dus-i.nl/btv</a>.
     Vermeld hierbij het kenmerk <em>{$content->reference}</em>.</p>
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        namens de minister van Volksgezondheid, Welzijn en Sport,<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        Aryan van Driel<br/>
        Algemeen directeur<br/>
        Dienst Uitvoering Subsidies aan Instellingen
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

-- d8c2a8d1-e512-40a1-94f8-6535cc85289c -- approved

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

{block questions}
    <h2>Vragen?</h2>
    <p>Neem contact op met DUS-I via telefoon: <a href="tel:+31703405566">070 3405566</a> of gebruik het contactformulier op <a href="https://www.dus-i.nl/btv">www.dus-i.nl/btv</a>.
     Vermeld hierbij het kenmerk <em>{$content->reference}</em>.</p>
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        namens de minister van Volksgezondheid, Welzijn en Sport,<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        Aryan van Driel<br/>
        Algemeen directeur<br/>
        Dienst Uitvoering Subsidies aan Instellingen
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

{block questions}
    <h2>Vragen?</h2>
    <p>Neem contact op met DUS-I via telefoon: <a href="tel:+31703405566">070 3405566</a> of gebruik het contactformulier op <a href="https://www.dus-i.nl/btv">www.dus-i.nl/btv</a>.
     Vermeld hierbij het kenmerk <em>{$content->reference}</em>.</p>
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        namens de minister van Volksgezondheid, Welzijn en Sport,<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        Aryan van Driel<br/>
        Algemeen directeur<br/>
        Dienst Uitvoering Subsidies aan Instellingen
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

-- d9917011-3baf-4a5f-8b1f-0e8e2b62d0a4 -- reclaimed

UPDATE public.subsidy_stage_transition_messages
SET content_html = '{layout ''letter_view_layout.latte''}

{block content}
    <p>Beste lezer,</p>

    <p>Bij brief van {$content->lastAllocatedAt|date:''d-m-Y''}, met kenmerk {$content->reference}, heb ik aan u een subsidie verleend van
        {$content->stage2->amount} voor de subsidieregeling borstprothesen transvrouwen (hierna: Subsidieregeling). In mijn
        administratie is uw aanvraag bekend onder nummer: {$content->reference}.</p>

    {var $motivation = isset($content->stage6?->motivation) ? $content->stage6->motivation : $content->stage5?->motivation}
    {if $motivation}
        <p>{$motivation|breakLines}</p>
    {/if}

    <h2>Besluit</h2>

    {var $newAssignationAmount = isset($content->stage6?->newAssignationAmount) ? $content->stage6->newAssignationAmount : $content->stage5->newAssignationAmount}
    <p>Ik stel de subsidie vast op een lager bedrag dan aan u is verleend, namelijk € {formatCurrency($newAssignationAmount)}.</p>

    <p>Mocht u in de toekomst alsnog gebruiken willen maken van de Subsidieregeling borstprothesen transvrouwen, dan dient u
        een volledig nieuwe aanvraag in te dienen, conform de vereisten die in artikel 7 van de regeling zijn opgenomen. U
        kunt zich niet beroepen op al eerder ingediende documenten.</p>

    <h2>Hoe wordt de subsidie afgehandeld?</h2>

    <p>De subsidie is geheel als voorschot aan u uitbetaald.</p>

    <p>Het verschil tussen het vastgestelde subsidiebedrag en het ontvangen voorschot bedraagt {$content->stage2->amount}. Ik vorder dit
        verschil van u terug op grond van artikel 4:57 van de Algemene wet bestuursrecht.</p>

    {var $reclaimAmount = isset($content->stage6?->reclaimAmount) ? $content->stage6->reclaimAmount : $content->stage5->reclaimAmount}
    <p>Ik verzoek u het bedrag van € {formatCurrency($reclaimAmount)} binnen zes weken na de datum van deze beschikking over te
        maken op bankrekeningnummer: NL55INGB0705003566 ten name van VWS — Financieel Dienstencentrum onder vermelding van
        het vorderingsnummer {$content->stage7->reclaimNumber} en subsidienummer {$content->reference}.</p>
{/block}

{block questions}
    <h2>Vragen?</h2>
    <p>Neem contact op met DUS-I via telefoon: <a href="tel:+31703405566">070 3405566</a> of gebruik het contactformulier op <a href="https://www.dus-i.nl/btv">www.dus-i.nl/btv</a>.
     Vermeld hierbij het kenmerk <em>{$content->reference}</em>.</p>
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        namens de minister van Volksgezondheid, Welzijn en Sport,<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        Aryan van Driel<br/>
        Algemeen directeur<br/>
        Dienst Uitvoering Subsidies aan Instellingen
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

    {var $motivation = isset($content->stage6?->motivation) ? $content->stage6->motivation : $content->stage5?->motivation}
    {if $motivation}
        <p>{$motivation|breakLines}</p>
    {/if}

    <h2>Besluit</h2>

    {var $newAssignationAmount = isset($content->stage6?->newAssignationAmount) ? $content->stage6->newAssignationAmount : $content->stage5->newAssignationAmount}
    <p>Ik stel de subsidie vast op een lager bedrag dan aan u is verleend, namelijk € {formatCurrency($newAssignationAmount)}.</p>

    <p>Mocht u in de toekomst alsnog gebruiken willen maken van de Subsidieregeling borstprothesen transvrouwen, dan dient u
        een volledig nieuwe aanvraag in te dienen, conform de vereisten die in artikel 7 van de regeling zijn opgenomen. U
        kunt zich niet beroepen op al eerder ingediende documenten.</p>

    <h2>Hoe wordt de subsidie afgehandeld?</h2>

    <p>De subsidie is geheel als voorschot aan u uitbetaald.</p>

    <p>Het verschil tussen het vastgestelde subsidiebedrag en het ontvangen voorschot bedraagt {$content->stage2->amount}. Ik vorder dit
        verschil van u terug op grond van artikel 4:57 van de Algemene wet bestuursrecht.</p>

    {var $reclaimAmount = isset($content->stage6?->reclaimAmount) ? $content->stage6->reclaimAmount : $content->stage5->reclaimAmount}
    <p>Ik verzoek u het bedrag van € {formatCurrency($reclaimAmount)} binnen zes weken na de datum van deze beschikking over te
        maken op bankrekeningnummer: NL55INGB0705003566 ten name van VWS — Financieel Dienstencentrum onder vermelding van
        het vorderingsnummer {$content->stage7->reclaimNumber} en subsidienummer {$content->reference}.</p>
{/block}

{block questions}
    <h2>Vragen?</h2>
    <p>Neem contact op met DUS-I via telefoon: <a href="tel:+31703405566">070 3405566</a> of gebruik het contactformulier op <a href="https://www.dus-i.nl/btv">www.dus-i.nl/btv</a>.
     Vermeld hierbij het kenmerk <em>{$content->reference}</em>.</p>
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        namens de minister van Volksgezondheid, Welzijn en Sport,<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        Aryan van Driel<br/>
        Algemeen directeur<br/>
        Dienst Uitvoering Subsidies aan Instellingen
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
