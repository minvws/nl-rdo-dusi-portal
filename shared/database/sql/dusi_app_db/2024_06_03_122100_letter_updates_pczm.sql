-- DUSI-1798 Beschikking post-COVID: de template voor de toewijzingsbrief en de afwijzingsbrief e-mailadres aanpassen naar: Post-COVID@minvws.nl
update public.subsidy_versions
set contact_mail_address = 'post-covid@minvws.nl'
where id = '513011cd-789b-4628-ba5c-2fee231f8959'; -- Version 1

update public.subsidy_versions
set contact_mail_address = 'post-covid@minvws.nl'
where id = '0185f897-99b0-4390-bd1f-98cce4bd578b'; -- Version 2

-- DUSI-1799 Behandelportaal post-COVID: Brief Herziening toekenning aanvraag automatisch toevoegen bij goedgekeurde aanvragen
UPDATE public.subsidy_stage_transition_messages
SET content_html = e'{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->stage5->closedAt|date:"d-m-Y"} ontving u een beslissing op uw aanvraag voor de regeling \'{$content->subsidyTitle}\' met referentienummer {$content->reference}.
        Uw aanvraag is toen toegekend. Namens het kabinet verleende ik u de eenmalige financiële ondersteuning van € 15.000.
    </p>

    <p>
        Dat betekent, tot mijn spijt, dat u geconfronteerd bent met de grote gevolgen van uw langdurige post-COVID klachten. Met de financiële ondersteuning wil het kabinet erkenning bieden voor uw getoonde inzet tijdens uw werk en het ontstane leed als gevolg van uw langdurige post-COVID klachten.
    </p>

    <p>
        Naar aanleiding van een nieuw kabinetsbesluit, informeer ik u in deze brief over de hoogte van de eenmalige financiële ondersteuning.
    </p>

    <h2>Herzieningsbesluit</h2>
    <p>Het kabinet heeft in april 2024 besloten om de eenmalige financiële ondersteuning te verhogen van € 15.000 naar € 24.010. Dit betekent dat u nog een bedrag van € 9.010 ontvangt.</p>

    <h2>Wanneer ontvangt u de financiële ondersteuning?</h2>
    <p>Wij betalen het bedrag van € 9.010 in één keer uit. Wij gebruiken hiervoor het rekeningnummer dat u eerder bij uw aanvraag aan ons doorgaf. Klopt dit rekeningnummer niet meer? Geef dit zo snel mogelijk aan ons door: 070-3405566. Wij streven ernaar het bedrag binnen 4 weken aan u over te maken.</p>

    <h2>Gevolgen voor belastingen en uitkeringen:</h2>
    <ul>
        <li>De financiële ondersteuning is eenmalig en telt daarom niet voor de inkomensbelasting (Box 1). Het heeft ook geen gevolgen voor uw WIA-uitkering. Het bedrag wordt wel onderdeel van het vermogen en kan hier belast worden (Box 3). Voor toeslagen, de bijstand en de eigen bijdragen voor zorg op basis van de Wet langdurige zorg of de Wet maatschappelijke ondersteuning 2015 wordt gekeken naar uw vermogen. Het kabinet heeft de financiële ondersteuning tijdelijk (voor een periode van 10 jaar) uitgezonderd voor deze vermogenstoets. Let op: U moet de uitzondering zelf aanvragen. Meer informatie staat bij de vragen en antwoorden op <a href="https://www.dus-i.nl/post-covid" target="_blank">www.dus-i.nl/post-covid</a>.
        </li>
    </ul>
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de Minister van Volksgezondheid, Welzijn en Sport,<br/>
        namens deze,<br/>
        het afdelingshoofd van Dienst Uitvoering Subsidies aan Instellingen<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(\'vws_dusi_signature.jpg\')|dataStream}" />
        <br/>
        L. van der Weij
    </p>
{/block}
',
    content_pdf                 = e'{layout \'letter_layout.latte\'}

{block concern}
    Betreft: Herziening toekenning aanvraag \'{$content->subsidyTitle}\'
{/block}

{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->stage5->closedAt|date:"d-m-Y"} ontving u een beslissing op uw aanvraag voor de regeling \'{$content->subsidyTitle}\' met referentienummer {$content->reference}.
        Uw aanvraag is toen toegekend. Namens het kabinet verleende ik u de eenmalige financiële ondersteuning van € 15.000.
    </p>

    <p>
        Dat betekent, tot mijn spijt, dat u geconfronteerd bent met de grote gevolgen van uw langdurige post-COVID klachten. Met de financiële ondersteuning wil het kabinet erkenning bieden voor uw getoonde inzet tijdens uw werk en het ontstane leed als gevolg van uw langdurige post-COVID klachten.
    </p>

    <p>
        Naar aanleiding van een nieuw kabinetsbesluit, informeer ik u in deze brief over de hoogte van de eenmalige financiële ondersteuning.
    </p>

    <h2>Herzieningsbesluit</h2>
    <p>Het kabinet heeft in april 2024 besloten om de eenmalige financiële ondersteuning te verhogen van € 15.000 naar € 24.010. Dit betekent dat u nog een bedrag van € 9.010 ontvangt.</p>

    <h2>Wanneer ontvangt u de financiële ondersteuning?</h2>
    <p>Wij betalen het bedrag van € 9.010 in één keer uit. Wij gebruiken hiervoor het rekeningnummer dat u eerder bij uw aanvraag aan ons doorgaf. Klopt dit rekeningnummer niet meer? Geef dit zo snel mogelijk aan ons door: 070-3405566. Wij streven ernaar het bedrag binnen 4 weken aan u over te maken.</p>

    <h2>Gevolgen voor belastingen en uitkeringen:</h2>
    <ul>
        <li>De financiële ondersteuning is eenmalig en telt daarom niet voor de inkomensbelasting (Box 1). Het heeft ook geen gevolgen voor uw WIA-uitkering. Het bedrag wordt wel onderdeel van het vermogen en kan hier belast worden (Box 3). Voor toeslagen, de bijstand en de eigen bijdragen voor zorg op basis van de Wet langdurige zorg of de Wet maatschappelijke ondersteuning 2015 wordt gekeken naar uw vermogen. Het kabinet heeft de financiële ondersteuning tijdelijk (voor een periode van 10 jaar) uitgezonderd voor deze vermogenstoets. Let op: U moet de uitzondering zelf aanvragen. Meer informatie staat bij de vragen en antwoorden op <a href="https://www.dus-i.nl/post-covid" target="_blank">www.dus-i.nl/post-covid</a>.
        </li>
    </ul>
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de Minister van Volksgezondheid, Welzijn en Sport,<br/>
        namens deze,<br/>
        het afdelingshoofd van Dienst Uitvoering Subsidies aan Instellingen<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(\'vws_dusi_signature.jpg\')|dataStream}" />
        <br/>
        L. van der Weij
    </p>
{/block}

{block sidebar}
    {include parent}
{/block}
', updated_at = 'now()'
WHERE id = 'd3dcc915-fdaf-472a-9f3c-d9a09dc263b3';

-- DUSI-1797 behandelportaal post-COVID: toekenningsbrief moet worden aangepast
UPDATE public.subsidy_stage_transition_messages
SET content_html = e'{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling \'{$content->subsidyTitle}\' met referentienummer {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>

    <h2>Besluit</h2>
    <p>
        Namens het kabinet verleen ik u hierbij de financiële ondersteuning van € {$content->stage2->amount}. Dat betekent, tot mijn spijt,
        dat u geconfronteerd bent met de grote gevolgen van uw langdurige post-COVID klachten. Met de financiële
        ondersteuning wil het kabinet erkenning bieden voor uw getoonde inzet tijdens uw werk en het ontstane leed als
        gevolg van uw langdurige post-COVID klachten.
    </p>

    {if $content->stage2->firstAssessmentApprovedNote}
    <h2>Motivering bij het besluit</h2>
    <p>{$content->stage2->firstAssessmentApprovedNote|breakLines}</p>
    {/if}

    <h2>Wanneer ontvangt u de financiële ondersteuning?</h2>
    <p>
        Wij betalen het bedrag van € {$content->stage2->amount} in één keer uit op het door u opgegeven rekeningnummer.
        Wij streven ernaar het bedrag binnen 4 weken aan u over te maken.
    </p>

    <h2>Verder willen wij u wijzen op de volgende punten:</h2>
    <ul>
        <li>De financiële ondersteuning is eenmalig en telt daarom niet voor de inkomensbelasting (Box 1). Het heeft ook
            geen gevolgen voor uw WIA-uitkering. Het bedrag wordt wel onderdeel van het vermogen en kan hier belast
            worden (Box 3). Voor toeslagen, de bijstand en de eigen bijdragen voor zorg op basis van de Wlz of Wmo wordt
            gekeken naar uw vermogen. Het kabinet werkt aan een tijdelijke uitzondering van deze financiële
            ondersteuning voor de vermogenstoets. <b>Let op</b>: U moet de uitzondering zelf aanvragen. Meer informatie staat
            bij de vragen en antwoorden op <a href="https://www.dus-i.nl/post-covid" target="_blank">www.dus-i.nl/post-covid</a>.
        </li>
    </ul>
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de Minister van Volksgezondheid, Welzijn en Sport,<br/>
        namens deze,<br/>
        het afdelingshoofd van Dienst Uitvoering Subsidies aan Instellingen<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(\'vws_dusi_signature.jpg\')|dataStream}" />
        <br/>
        L. van der Weij
    </p>
{/block}
',
    content_pdf                 = e'{layout \'letter_layout.latte\'}

{block concern}
    Betreft: Toekenning aanvraag \'{$content->subsidyTitle}\'
{/block}

{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling \'{$content->subsidyTitle}\' met referentienummer {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>

    <h2>Besluit</h2>
    <p>
        Namens het kabinet verleen ik u hierbij de financiële ondersteuning van € {$content->stage2->amount}. Dat betekent, tot mijn spijt,
        dat u geconfronteerd bent met de grote gevolgen van uw langdurige post-COVID klachten. Met de financiële
        ondersteuning wil het kabinet erkenning bieden voor uw getoonde inzet tijdens uw werk en het ontstane leed als
        gevolg van uw langdurige post-COVID klachten.
    </p>

    {if $content->stage2->firstAssessmentApprovedNote}
        <h2>Motivering bij het besluit</h2>
        <p>{$content->stage2->firstAssessmentApprovedNote|breakLines}</p>
    {/if}

    <h2>Wanneer ontvangt u de financiële ondersteuning?</h2>
    <p>
        Wij betalen het bedrag van € {$content->stage2->amount} in één keer uit op het door u opgegeven rekeningnummer.
         Wij streven ernaar het bedrag binnen 4 weken aan u over te maken.
    </p>

    <h2>Verder willen wij u wijzen op de volgende punten:</h2>
    <ul>
        <li>De financiële ondersteuning is eenmalig en telt daarom niet voor de inkomensbelasting (Box 1). Het heeft ook
            geen gevolgen voor uw WIA-uitkering. Het bedrag wordt wel onderdeel van het vermogen en kan hier belast
            worden (Box 3). Voor toeslagen, de bijstand en de eigen bijdragen voor zorg op basis van de Wlz of Wmo wordt
            gekeken naar uw vermogen. Het kabinet werkt aan een tijdelijke uitzondering van deze financiële
            ondersteuning voor de vermogenstoets. <b>Let op</b>: U moet de uitzondering zelf aanvragen. Meer informatie staat
            bij de vragen en antwoorden op <a href="https://www.dus-i.nl/post-covid" target="_blank">www.dus-i.nl/post-covid</a>.
        </li>
    </ul>
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de Minister van Volksgezondheid, Welzijn en Sport,<br/>
        namens deze,<br/>
        het afdelingshoofd van Dienst Uitvoering Subsidies aan Instellingen<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(\'vws_dusi_signature.jpg\')|dataStream}" />
        <br/>
        L. van der Weij
    </p>
{/block}

{block sidebar}
    {include parent}
{/block}
', updated_at = 'now()'
WHERE id = '8908ef68-5241-4b4e-961f-304b53f3695c';

