-- AIGT
-- TRANSITION_STAGE_2_TO_1_MESSAGE - c6410597-cbc0-45f4-aa0c-3d8631d661f2

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
        de Minister voor Medische Zorg,<br/>
        namens deze,<br/>
        het afdelingshoofd van Dienst Uitvoering Subsidies aan Instellingen<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        L. van der Weij
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
        de Minister voor Medische Zorg,<br/>
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

    <h4>Relatienummer</h4>
    <p>{$content->stage2->businessPartnerNumber}</p>

    <h4>Verplichtingennummer</h4>
    <p>{$content->stage2->liabilitiesNumber}</p>
{/block}


{block objectionFooter}{/block}
',
    updated_at = 'now()'
WHERE id = 'c6410597-cbc0-45f4-aa0c-3d8631d661f2';


-- TRANSITION_STAGE_4_TO_REJECTED_MESSAGE - b135a0f1-c584-4f69-bbad-e9db91a0de6d

UPDATE public.subsidy_stage_transition_messages
SET content_html = '{layout ''letter_view_layout.latte''}

{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling ''{$content->subsidyTitle}'' met referentienummer {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>

    <h2>Besluit</h2>
    <p>Uw subsidieaanvraag moet ik helaas afwijzen. De reden(en) licht ik hieronder toe.</p>

    {if $content->stage2->firstAssessmentRejectedNote}
        <h2>Motivering bij het besluit</h2>
        <p>Uw aanvraag is beoordeeld op grond van:</p>
        <ul>
            <li>Kaderregeling subsidies OCW, SZW en VWS;</li>
            <li>Subsidieregeling opleidingsactiviteiten Arts Internationale Gezondheid en Tropengeneeskunde 2021-2026;</li>
            <li>Algemene wet bestuursrecht, in het bijzonder titel 4.2 Subsidies;</li>
            <li>Profielregister RGS en opleidingsregister OIGT.</li>
        </ul>
        <p>Uw aanvraag voldoet niet aan de volgende voorwaarde(n):</p>
        <p>{$content->stage2->firstAssessmentRejectedNote|breakLines}</p>
    {/if}
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de Minister voor Medische Zorg,<br/>
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

    <h2>Besluit</h2>
    <p>Uw subsidieaanvraag moet ik helaas afwijzen. De reden(en) licht ik hieronder toe.</p>

    {if $content->stage2->firstAssessmentRejectedNote}
        <h2>Motivering bij het besluit</h2>
        <p>Uw aanvraag is beoordeeld op grond van:</p>
        <ul>
            <li>Kaderregeling subsidies OCW, SZW en VWS;</li>
            <li>Subsidieregeling opleidingsactiviteiten Arts Internationale Gezondheid en Tropengeneeskunde 2021-2026;</li>
            <li>Algemene wet bestuursrecht, in het bijzonder titel 4.2 Subsidies;</li>
            <li>Profielregister RGS en opleidingsregister OIGT.</li>
        </ul>
        <p>Uw aanvraag voldoet niet aan de volgende voorwaarde(n):</p>
        <p>{$content->stage2->firstAssessmentRejectedNote|breakLines}</p>
    {/if}
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de Minister voor Medische Zorg,<br/>
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

    <h4>Relatienummer</h4>
    <p>{$content->stage2->businessPartnerNumber}</p>

    <h4>Verplichtingennummer</h4>
    <p>{$content->stage2->liabilitiesNumber}</p>
{/block}
',
    updated_at = 'now()'
WHERE id = 'b135a0f1-c584-4f69-bbad-e9db91a0de6d';


-- TRANSITION_STAGE_4_TO_5_ALLOCATED_MESSAGE - ef41a929-6556-4dec-975e-5d75f5a48a64

UPDATE public.subsidy_stage_transition_messages
SET content_html = '{layout ''letter_view_layout.latte''}

{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling ''{$content->subsidyTitle}'' met referentienummer {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>

    <h2>Besluit</h2>
    <p>Hierbij verleen ik u de subsidie van {$content->stage2->amount}.</p>

    <p>De subsidie is gebaseerd op artikel 1.2 van de Kaderregeling subsidies OCW, SZW en VWS en de Subsidieregeling opleidingsactiviteiten Arts Internationale Gezondheid en Tropengeneeskunde 2021-2026.
       Dit is een projectsubsidie als bedoeld in artikel 1.5, onder a 1° van de Kaderregeling subsidies OCW, SZW en VWS.
       De subsidie wordt eenmalig verstrekt ter compensatie van de kosten voor het volgen van het opleidingsonderdeel “Buitenland’ van de opleiding tot Arts Internationale Gezondheid en Tropengeneeskunde.
   </p>

    {if $content->stage2->firstAssessmentApprovedNote}
        <h2>Motivering bij het besluit</h2>
        <p>{$content->stage2->firstAssessmentApprovedNote|breakLines}</p>
    {/if}

    <h2>Waaraan moet u voldoen?</h2>
    <p>U moet voldoen aan de verplichtingen in de wet- en regelgeving in deze beschikking.</p>

    <p><u>Wet- en regelgeving</u><br/>
        Op deze subsidie zijn de volgende wet- en regelgeving en registers van toepassing:
    </p>
    <ul>
        <li>Kaderregeling subsidies OCW, SZW en VWS;</li>
        <li>Subsidieregeling opleidingsactiviteiten Arts Internationale Gezondheid en Tropengeneeskunde 2021-2026;</li>
        <li>Algemene wet bestuursrecht, in het bijzonder titel 4.2 Subsidies;</li>
        <li>Profielregister RGS en opleidingsregister OIGT.</li>
    </ul>

    <p><u>Meldingsplicht</u><br />
        U bent verplicht een melding te maken bij de Dienst Uitvoering Subsidies aan Instellingen (DUS-I) wanneer niet,
        niet op tijd of niet geheel zal worden voldaan aan de verplichtingen die aan de subsidie zijn verbonden.
    </p>

    <p><u>Wat als u zich niet aan de voorschriften houdt?</u>
        Het niet voldoen aan de verplichtingen kan tot gevolg hebben dat u de subsidie helemaal of gedeeltelijk moet terugbetalen.
    </p>

    <h2>Wanneer ontvangt u de subsidie?</h2>
    <p>Ik streef ernaar het toegekende subsidiebedrag binnen 10 werkdagen naar u over te maken onder vermelding van het
        referentienummer {$content->reference}.</p>

    <h2>Wanneer wordt de subsidie vastgesteld?</h2>
    <p>Tenzij u hierover een ander bericht ontvangt, stel ik de subsidie vast binnen 22 weken na afronding van de
        opleidingsactiviteiten. De vaststelling vindt plaats in een afzonderlijke beschikking.</p>
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de Minister voor Medische Zorg,<br/>
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
    Betreft: Verlening aanvraag ''{$content->subsidyTitle}''
{/block}

{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling ''{$content->subsidyTitle}'' met referentienummer {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>

    <h2>Besluit</h2>
    <p>Hierbij verleen ik u de subsidie van {$content->stage2->amount}.</p>

    <p>De subsidie is gebaseerd op artikel 1.2 van de Kaderregeling subsidies OCW, SZW en VWS en de Subsidieregeling opleidingsactiviteiten Arts Internationale Gezondheid en Tropengeneeskunde 2021-2026.
       Dit is een projectsubsidie als bedoeld in artikel 1.5, onder a 1° van de Kaderregeling subsidies OCW, SZW en VWS.
       De subsidie wordt eenmalig verstrekt ter compensatie van de kosten voor het volgen van het opleidingsonderdeel “Buitenland’ van de opleiding tot Arts Internationale Gezondheid en Tropengeneeskunde.
   </p>

    {if $content->stage2->firstAssessmentApprovedNote}
        <h2>Motivering bij het besluit</h2>
        <p>{$content->stage2->firstAssessmentApprovedNote|breakLines}</p>
    {/if}

    <h2>Waaraan moet u voldoen?</h2>
    <p>U moet voldoen aan de verplichtingen in de wet- en regelgeving in deze beschikking.</p>

    <p><u>Wet- en regelgeving</u><br/>
        Op deze subsidie zijn de volgende wet- en regelgeving en registers van toepassing:
    </p>
    <ul>
        <li>Kaderregeling subsidies OCW, SZW en VWS;</li>
        <li>Subsidieregeling opleidingsactiviteiten Arts Internationale Gezondheid en Tropengeneeskunde 2021-2026;</li>
        <li>Algemene wet bestuursrecht, in het bijzonder titel 4.2 Subsidies;</li>
        <li>Profielregister RGS en opleidingsregister OIGT.</li>
    </ul>

    <p><u>Meldingsplicht</u><br />
        U bent verplicht een melding te maken bij de Dienst Uitvoering Subsidies aan Instellingen (DUS-I) wanneer niet,
        niet op tijd of niet geheel zal worden voldaan aan de verplichtingen die aan de subsidie zijn verbonden.
    </p>

    <p><u>Wat als u zich niet aan de voorschriften houdt?</u>
        Het niet voldoen aan de verplichtingen kan tot gevolg hebben dat u de subsidie helemaal of gedeeltelijk moet terugbetalen.
    </p>

    <h2>Wanneer ontvangt u de subsidie?</h2>
    <p>Ik streef ernaar het toegekende subsidiebedrag binnen 10 werkdagen naar u over te maken onder vermelding van het
        referentienummer {$content->reference}.</p>

    <h2>Wanneer wordt de subsidie vastgesteld?</h2>
    <p>Tenzij u hierover een ander bericht ontvangt, stel ik de subsidie vast binnen 22 weken na afronding van de
        opleidingsactiviteiten. De vaststelling vindt plaats in een afzonderlijke beschikking.</p>
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de Minister voor Medische Zorg,<br/>
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

    <h4>Relatienummer</h4>
    <p>{$content->stage2->businessPartnerNumber}</p>

    <h4>Verplichtingennummer</h4>
    <p>{$content->stage2->liabilitiesNumber}</p>
{/block}
',
    updated_at = 'now()'
WHERE id = 'ef41a929-6556-4dec-975e-5d75f5a48a64';


-- TRANSITION_STAGE_8_TO_APPROVED_MESSAGE - be7c6a5e-24a5-44d2-8e13-f259651e72e0

UPDATE public.subsidy_stage_transition_messages
SET content_html = '{layout ''letter_view_layout.latte''}

{block content}
    <p>Beste lezer,</p>
    <p>Met mijn brief van {$content->stage4->closedAt|date:"d-m-Y"} heb ik u een subsidie verleend van
        {$content->stage2->amount} voor het onderdeel "Buitenland" van de opleiding AIGT. Uit de aan mij ter beschikking
        gestelde gegevens blijkt dat u inmiddels het opleidingsonderdeel "Buitenland" van de opleiding AIGT heeft afgerond.
    </p>

    <h2>Besluit</h2>
    <p>Ik stel hierbij de subsidie vast op {$content->stage2->amount}.</p>

    <h2>Motivering bij het besluit</h2>
    <p>De subsidie is gebaseerd op artikel 1.2 van de Kaderregeling subsidies OCW, SZW en VWS en de Subsidieregeling
        opleidingsactiviteiten Arts Internationale Gezondheid en Tropengeneeskunde 2021-2026.
        Dit is een projectsubsidie als bedoeld in artikel 1.5, onder a 1° van de Kaderregeling subsidies OCW, SZW en
        VWS.
        De subsidie wordt eenmalig verstrekt ter compensatie van de kosten voor het volgen van het opleidingsonderdeel
        “Buitenland’ van de opleiding tot Arts Internationale Gezondheid en Tropengeneeskunde.
    </p>

    <p><u>Wet- en regelgeving</u><br/>
        De subsidie is vastgesteld op grond van:
    </p>
    <ul>
        <li>Kaderregeling subsidies OCW, SZW en VWS;</li>
        <li>Subsidieregeling opleidingsactiviteiten Arts Internationale Gezondheid en Tropengeneeskunde 2021-2026;</li>
        <li>Algemene wet bestuursrecht, in het bijzonder titel 4.2 Subsidies;</li>
        <li>Profielregister RGS en opleidingsregister OIGT.</li>
    </ul>

    <h2>Hoe wordt de subsidie verder afgehandeld?</h2>
    <p>U hebt inmiddels het volledige subsidiebedrag van {$content->stage2->amount} ontvangen.</p>
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de Minister voor Medische Zorg,<br/>
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
    Betreft: Vaststelling subsidie ''{$content->subsidyTitle}''
{/block}

{block content}
    <p>Beste lezer,</p>
    <p>Met mijn brief van {$content->stage4->closedAt|date:"d-m-Y"} heb ik u een subsidie verleend van
        {$content->stage2->amount} voor het onderdeel "Buitenland" van de opleiding AIGT. Uit de aan mij ter beschikking
        gestelde gegevens blijkt dat u inmiddels het opleidingsonderdeel "Buitenland" van de opleiding AIGT heeft afgerond.
    </p>

    <h2>Besluit</h2>
    <p>Ik stel hierbij de subsidie vast op {$content->stage2->amount}.</p>

    <h2>Motivering bij het besluit</h2>
    <p>De subsidie is gebaseerd op artikel 1.2 van de Kaderregeling subsidies OCW, SZW en VWS en de Subsidieregeling
        opleidingsactiviteiten Arts Internationale Gezondheid en Tropengeneeskunde 2021-2026.
        Dit is een projectsubsidie als bedoeld in artikel 1.5, onder a 1° van de Kaderregeling subsidies OCW, SZW en
        VWS.
        De subsidie wordt eenmalig verstrekt ter compensatie van de kosten voor het volgen van het opleidingsonderdeel
        “Buitenland’ van de opleiding tot Arts Internationale Gezondheid en Tropengeneeskunde.
    </p>

    <p><u>Wet- en regelgeving</u><br/>
        De subsidie is vastgesteld op grond van:
    </p>
    <ul>
        <li>Kaderregeling subsidies OCW, SZW en VWS;</li>
        <li>Subsidieregeling opleidingsactiviteiten Arts Internationale Gezondheid en Tropengeneeskunde 2021-2026;</li>
        <li>Algemene wet bestuursrecht, in het bijzonder titel 4.2 Subsidies;</li>
        <li>Profielregister RGS en opleidingsregister OIGT.</li>
    </ul>

    <h2>Hoe wordt de subsidie verder afgehandeld?</h2>
    <p>U hebt inmiddels het volledige subsidiebedrag van {$content->stage2->amount} ontvangen.</p>
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de Minister voor Medische Zorg,<br/>
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

    <h4>Relatienummer</h4>
    <p>{$content->stage2->businessPartnerNumber}</p>

    <h4>Verplichtingennummer</h4>
    <p>{$content->stage2->liabilitiesNumber}</p>
{/block}
',
    updated_at = 'now()'
WHERE id = 'be7c6a5e-24a5-44d2-8e13-f259651e72e0';


-- TRANSITION_STAGE_8_TO_RECLAIM_MESSAGE - b8cfccbc-d9ba-463f-8cbc-4930057a0dff

UPDATE public.subsidy_stage_transition_messages
SET content_html = '{layout ''letter_view_layout.latte''}

{block content}
    <p>Beste lezer,</p>
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de Minister voor Medische Zorg,<br/>
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
    Betreft: Vordering subsidie ''{$content->subsidyTitle}''
{/block}

{block content}
    <p>Beste lezer,</p>
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de Minister voor Medische Zorg,<br/>
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

    <h4>Relatienummer</h4>
    <p>{$content->stage2->businessPartnerNumber}</p>

    <h4>Verplichtingennummer</h4>
    <p>{$content->stage2->liabilitiesNumber}</p>
{/block}
',
    updated_at = 'now()'
WHERE id = 'b8cfccbc-d9ba-463f-8cbc-4930057a0dff';
