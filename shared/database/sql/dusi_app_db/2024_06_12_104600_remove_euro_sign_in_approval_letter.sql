UPDATE public.subsidy_stage_transition_messages
SET
    content_html                = e'{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling \'{$content->subsidyTitle}\' met referentienummer {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>

    <h2>Besluit</h2>
    <p>
        Namens het kabinet verleen ik u hierbij de financiële ondersteuning van {$content->stage2->amount}. Dat betekent, tot mijn spijt,
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
        Wij betalen het bedrag van {$content->stage2->amount} in één keer uit op het door u opgegeven rekeningnummer.
        Wij streven ernaar het bedrag binnen 4 weken aan u over te maken.
    </p>

    <h2>Gevolgen voor belastingen en uitkeringen</h2>
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
        Namens het kabinet verleen ik u hierbij de financiële ondersteuning van {$content->stage2->amount}. Dat betekent, tot mijn spijt,
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
        Wij betalen het bedrag van {$content->stage2->amount} in één keer uit op het door u opgegeven rekeningnummer.
         Wij streven ernaar het bedrag binnen 4 weken aan u over te maken.
    </p>

    <h2>Gevolgen voor belastingen en uitkeringen</h2>
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
',
    updated_at                  = 'now()'
WHERE id = '8908ef68-5241-4b4e-961f-304b53f3695c';
