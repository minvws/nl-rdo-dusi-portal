-- Update PCZM message PCZMSubsidyStageTransitionsSeeder::PZCM_TRANSITION_STAGE_5_TO_APPROVED
-- PZCM_TRANSITION_STAGE_5_TO_APPROVED_MESSAGE

UPDATE public.subsidy_stage_transition_messages
SET content_html = '{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling ''{$content->subsidyTitle}'' met referentienummer {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>

    <h2>Besluit</h2>
    <p>Namens het kabinet verleen ik u hierbij de financiële ondersteuning van {$content->stage2->amount} volgens de ‘Regeling
        zorgmedewerkers met langdurige post-COVID klachten’. U ontvangt dit als gebaar ter erkenning voor uw ontstane
        leed en uw getoonde inzet in de zorg tijdens de uitzonderlijke situatie van de eerste golf in de
        COVID-pandemie.</p>

    {if $content->stage2->firstAssessmentApprovedNote}
        <h2>Motivering bij het besluit</h2>
        <p>{$content->stage2->firstAssessmentApprovedNote|breakLines}</p>
    {/if}

    <h2>Wanneer ontvangt u de financiële ondersteuning?</h2>
    <p>Het bedrag van {$content->stage2->amount} wordt in één keer uitbetaald. Wij streven ernaar de financiële
        ondersteuning binnen 10 werkdagen uit te keren.</p>

    <h2>Verder willen wij u wijzen op de volgende punten:</h2>
    <ul>
        <li>De financiële ondersteuning is eenmalig en telt daarom niet voor de inkomensbelasting (Box 1). Het heeft ook
            geen gevolgen voor uw WIA-uitkering. Het bedrag wordt wel onderdeel van het vermogen en kan hier belast
            worden (Box 3). Voor toeslagen, de bijstand en de eigen bijdragen voor zorg op basis van de Wlz of Wmo wordt
            gekeken naar uw vermogen. Het kabinet werkt aan een tijdelijke uitzondering van deze financiële
            ondersteuning voor de vermogenstoets. Op de website van DUS-I kunt u lezen of dit voor u relevant is en hoe
            u de uitzondering kunt aanvragen. Let op: U moet de uitzondering dus zelf aanvragen.
        </li>
        <li>De financiële ondersteuning wordt direct vastgesteld. Dat betekent dat u geen verantwoording hoeft in te
            dienen.
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
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        L. van der Weij
    </p>
{/block}
',
    content_pdf = '{layout ''letter_layout.latte''}

{block concern}
    Betreft: Toekenning aanvraag ''{$content->subsidyTitle}''
{/block}

{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling ''{$content->subsidyTitle}'' met referentienummer {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>

    <h2>Besluit</h2>
    <p>Namens het kabinet verleen ik u hierbij de financiële ondersteuning van {$content->stage2->amount} volgens de ‘Regeling
        zorgmedewerkers met langdurige post-COVID klachten’. U ontvangt dit als gebaar ter erkenning voor uw ontstane
        leed en uw getoonde inzet in de zorg tijdens de uitzonderlijke situatie van de eerste golf in de
        COVID-pandemie.</p>

    {if $content->stage2->firstAssessmentApprovedNote}
        <h2>Motivering bij het besluit</h2>
        <p>{$content->stage2->firstAssessmentApprovedNote|breakLines}</p>
    {/if}

    <h2>Wanneer ontvangt u de financiële ondersteuning?</h2>
    <p>Het bedrag van {$content->stage2->amount} wordt in één keer uitbetaald. Wij streven ernaar de financiële
        ondersteuning binnen 10 werkdagen uit te keren.</p>

    <h2>Verder willen wij u wijzen op de volgende punten:</h2>
    <ul>
        <li>De financiële ondersteuning is eenmalig en telt daarom niet voor de inkomensbelasting (Box 1). Het heeft ook
            geen gevolgen voor uw WIA-uitkering. Het bedrag wordt wel onderdeel van het vermogen en kan hier belast
            worden (Box 3). Voor toeslagen, de bijstand en de eigen bijdragen voor zorg op basis van de Wlz of Wmo wordt
            gekeken naar uw vermogen. Het kabinet werkt aan een tijdelijke uitzondering van deze financiële
            ondersteuning voor de vermogenstoets. Op de website van DUS-I kunt u lezen of dit voor u relevant is en hoe
            u de uitzondering kunt aanvragen. Let op: U moet de uitzondering dus zelf aanvragen.
        </li>
        <li>De financiële ondersteuning wordt direct vastgesteld. Dat betekent dat u geen verantwoording hoeft in te
            dienen.
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
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        L. van der Weij
    </p>
{/block}

{block sidebar}
    {include parent}
{/block}
',
    updated_at = 'now()'
WHERE id = '9c2ad81e-cf52-41a3-966f-fc9757de15c9';


-- Update PCZM message PCZMSubsidyStageTransitionsSeeder::PZCM_TRANSITION_STAGE_6_TO_INCREASED_GRANT_EMAIL
-- PZCM_TRANSITION_STAGE_6_TO_INCREASED_MESSAGE

UPDATE public.subsidy_stage_transition_messages
SET content_html = '{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->stage5->closedAt|date:"d-m-Y"} ontving u een beslissing op uw aanvraag voor de regeling ''{$content->subsidyTitle}'' met referentienummer {$content->reference}.
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
    <p>Wij betalen het bedrag van € 9.010 in één keer uit. Wij gebuiken hiervoor het rekeningnummer dat u eerder bij uw aanvraag aan ons doorgaf. Klopt dit rekeningnummer niet meer? Geef dit zo snel mogelijk aan ons door. Wij streven ernaar het bedrag binnen 4 weken aan u over te maken.</p>

    <h2>Verder willen wij u wijzen op de volgende punten:</h2>
    <ul>
        <li>De financiële ondersteuning is eenmalig en telt daarom niet voor de inkomensbelasting (Box 1). Het heeft ook geen gevolgen voor uw WIA-uitkering. Het bedrag wordt wel onderdeel van het vermogen en kan hier belast worden (Box 3). Voor toeslagen, de bijstand en de eigen bijdragen voor zorg op basis van de Wet langdurige zorg of de Wet maatschappelijke ondersteuning 2015 wordt gekeken naar uw vermogen. Het kabinet heeft de financiële ondersteuning tijdelijk (voor een periode van 10 jaar) uitgezonderd voor deze vermogenstoets. Op de website van DUS-I kunt u lezen of dit voor u relevant is en hoe u de uitzondering kunt aanvragen. Let op: U moet deze uitzondering dus zelf aanvragen.</li>
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
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        L. van der Weij
    </p>
{/block}
',
    content_pdf = '{layout ''letter_layout.latte''}

{block concern}
    Betreft: Herziening toekenning aanvraag ''{$content->subsidyTitle}''
{/block}

{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->stage5->closedAt|date:"d-m-Y"} ontving u een beslissing op uw aanvraag voor de regeling ''{$content->subsidyTitle}'' met referentienummer {$content->reference}.
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
    <p>Wij betalen het bedrag van € 9.010 in één keer uit. Wij gebuiken hiervoor het rekeningnummer dat u eerder bij uw aanvraag aan ons doorgaf. Klopt dit rekeningnummer niet meer? Geef dit zo snel mogelijk aan ons door. Wij streven ernaar het bedrag binnen 4 weken aan u over te maken.</p>

    <h2>Verder willen wij u wijzen op de volgende punten:</h2>
    <ul>
        <li>De financiële ondersteuning is eenmalig en telt daarom niet voor de inkomensbelasting (Box 1). Het heeft ook geen gevolgen voor uw WIA-uitkering. Het bedrag wordt wel onderdeel van het vermogen en kan hier belast worden (Box 3). Voor toeslagen, de bijstand en de eigen bijdragen voor zorg op basis van de Wet langdurige zorg of de Wet maatschappelijke ondersteuning 2015 wordt gekeken naar uw vermogen. Het kabinet heeft de financiële ondersteuning tijdelijk (voor een periode van 10 jaar) uitgezonderd voor deze vermogenstoets. Op de website van DUS-I kunt u lezen of dit voor u relevant is en hoe u de uitzondering kunt aanvragen. Let op: U moet deze uitzondering dus zelf aanvragen.</li>
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
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        L. van der Weij
    </p>
{/block}

{block sidebar}
    {include parent}
{/block}
',
    updated_at = 'now()'
WHERE id = 'd3dcc915-fdaf-472a-9f3c-d9a09dc263b3';


-- Update PCZM message PCZMSubsidyStageTransitionsSeeder::PZCM_TRANSITION_STAGE_3_TO_REJECTED
-- PZCM_TRANSITION_STAGE_3_TO_REJECTED_MESSAGE

UPDATE public.subsidy_stage_transition_messages
SET content_html = '{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling ''{$content->subsidyTitle}'' met referentienummer {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>
    <p>&nbsp;</p>

    <h2>Besluit</h2>
    <p>Uw aanvraag voor financiële ondersteuning moet ik helaas afwijzen. De reden(en) licht ik hieronder toe.</p>
    <p>&nbsp;</p>

    {if $content->stage2->firstAssessmentRejectedNote}
        <h2>Motivering bij het besluit</h2>
        <p>Uw aanvraag wordt afgewezen vanwege de volgende reden(en):</p>
        <p>{$content->stage2->firstAssessmentRejectedNote|breakLines}</p>
        <p>&nbsp;</p>
    {/if}
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de Minister van Volksgezondheid, Welzijn en Sport,<br/>
        namens deze,<br/>
        het afdelingshoofd van Dienst Uitvoering Subsidies aan Instellingen<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        L. van der Weij
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
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling ''{$content->subsidyTitle}'' met referentienummer {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>
    <p>&nbsp;</p>

    <h2>Besluit</h2>
    <p>Uw aanvraag voor financiële ondersteuning moet ik helaas afwijzen. De reden(en) licht ik hieronder toe.</p>
    <p>&nbsp;</p>

    {if $content->stage2->firstAssessmentRejectedNote}
        <h2>Motivering bij het besluit</h2>
        <p>Uw aanvraag wordt afgewezen vanwege de volgende reden(en):</p>
        <p>{$content->stage2->firstAssessmentRejectedNote|breakLines}</p>
        <p>&nbsp;</p>
    {/if}
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de Minister van Volksgezondheid, Welzijn en Sport,<br/>
        namens deze,<br/>
        het afdelingshoofd van Dienst Uitvoering Subsidies aan Instellingen<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        L. van der Weij
    </p>
{/block}

{block sidebar}
    {include parent}
{/block}
',
    updated_at = 'now()'
WHERE id = '64a636d8-ed0c-4bb6-982e-f948c68755b6';



-- Update PCZM message PCZMSubsidyStageTransitionsSeeder::PZCM_TRANSITION_STAGE_5_TO_REJECTED
-- PZCM_TRANSITION_STAGE_5_TO_REJECTED_MESSAGE

UPDATE public.subsidy_stage_transition_messages
SET content_html = '{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling ''{$content->subsidyTitle}'' met referentienummer {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>
    <p>&nbsp;</p>

    <h2>Besluit</h2>
    <p>Uw aanvraag voor financiële ondersteuning moet ik helaas afwijzen. De reden(en) licht ik hieronder toe.</p>
    <p>&nbsp;</p>

    {if $content->stage2->firstAssessmentRejectedNote}
        <h2>Motivering bij het besluit</h2>
        <p>Uw aanvraag wordt afgewezen vanwege de volgende reden(en):</p>
        <p>{$content->stage2->firstAssessmentRejectedNote|breakLines}</p>
        <p>&nbsp;</p>
    {/if}
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de Minister van Volksgezondheid, Welzijn en Sport,<br/>
        namens deze,<br/>
        het afdelingshoofd van Dienst Uitvoering Subsidies aan Instellingen<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        L. van der Weij
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
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling ''{$content->subsidyTitle}'' met referentienummer {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>
    <p>&nbsp;</p>

    <h2>Besluit</h2>
    <p>Uw aanvraag voor financiële ondersteuning moet ik helaas afwijzen. De reden(en) licht ik hieronder toe.</p>
    <p>&nbsp;</p>

    {if $content->stage2->firstAssessmentRejectedNote}
        <h2>Motivering bij het besluit</h2>
        <p>Uw aanvraag wordt afgewezen vanwege de volgende reden(en):</p>
        <p>{$content->stage2->firstAssessmentRejectedNote|breakLines}</p>
        <p>&nbsp;</p>
    {/if}
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de Minister van Volksgezondheid, Welzijn en Sport,<br/>
        namens deze,<br/>
        het afdelingshoofd van Dienst Uitvoering Subsidies aan Instellingen<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        L. van der Weij
    </p>
{/block}

{block sidebar}
    {include parent}
{/block}
',
    updated_at = 'now()'
WHERE id = '7da32b2f-4f0d-44ab-bc87-07718db4bfd5';



-- Update PCZM message PCZMSubsidyStageTransitionsSeeder::PZCM_TRANSITION_STAGE_2_TO_1
-- PCZM_TRANSITION_STAGE_2_TO_1_MESSAGE

UPDATE public.subsidy_stage_transition_messages
SET content_html = '{block content}
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
    <p>&nbsp;</p>

    <h2>Termijn</h2>
    <p>
        Ik ontvang de informatie graag binnen 2 weken na dagtekening van deze brief, dus uiterlijk {$content->createdAt->addDays(14)|date:"d-m-Y"}.
    </p>
    <p>
        U kunt de ontbrekende informatie aan uw aanvraag toevoegen door het door u ingevulde aanvraagformulier te heropenen in Mijn DUS-I.
    </p>
    <p>
        Als de gevraagde gegevens niet binnen 2 weken door mij zijn ontvangen, of onvoldoende zijn voor verdere
        beoordeling, dan kan uw aanvraag niet verder worden behandeld.
    </p>
    <p>&nbsp;</p>
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de Minister van Volksgezondheid, Welzijn en Sport,<br/>
        namens deze,<br/>
        het afdelingshoofd van Dienst Uitvoering Subsidies aan Instellingen<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        L. van der Weij
    </p>
{/block}
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
    <p>&nbsp;</p>

    <h2>Termijn</h2>
    <p>
        Ik ontvang de informatie graag binnen 2 weken na dagtekening van deze brief, dus uiterlijk {$content->createdAt->addDays(14)|date:"d-m-Y"}.
    </p>
    <p>
        U kunt de ontbrekende informatie aan uw aanvraag toevoegen door het door u ingevulde aanvraagformulier te heropenen in Mijn DUS-I.
    </p>
    <p>
        Als de gevraagde gegevens niet binnen 2 weken door mij zijn ontvangen, of onvoldoende zijn voor verdere
        beoordeling, dan kan uw aanvraag niet verder worden behandeld.
    </p>
    <p>&nbsp;</p>
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de Minister van Volksgezondheid, Welzijn en Sport,<br/>
        namens deze,<br/>
        het afdelingshoofd van Dienst Uitvoering Subsidies aan Instellingen<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        L. van der Weij
    </p>
{/block}

{block sidebar}
    {include parent}
{/block}

{block objectionFooter}{/block}
',
    updated_at = 'now()'
WHERE id = '85bf054e-c6e3-42d2-880d-07c29d0fe6bf';

