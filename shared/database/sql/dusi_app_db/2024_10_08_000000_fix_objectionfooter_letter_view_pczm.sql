-- PCZM
-- PCZM_TRANSITION_STAGE_2_TO_1_MESSAGE - 85bf054e-c6e3-42d2-880d-07c29d0fe6bf

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
        Als de gevraagde gegevens niet binnen 2 weken door mij zijn ontvangen, of onvoldoende zijn voor verdere
        beoordeling, dan kan uw aanvraag niet verder worden behandeld.
    </p>
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

{block objectionFooter}{/block}
',
    updated_at = 'now()'
WHERE id = '85bf054e-c6e3-42d2-880d-07c29d0fe6bf';

-- PCZMv2
-- TRANSITION_STAGE_2_TO_1_MESSAGE - 961023c7-82a9-40bb-b6b7-cd3882220681

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
        Als de gevraagde gegevens niet binnen 2 weken door mij zijn ontvangen, of onvoldoende zijn voor verdere
        beoordeling, dan kan uw aanvraag niet verder worden behandeld.
    </p>
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

{block objectionFooter}{/block}
',
    updated_at = 'now()'
WHERE id = '961023c7-82a9-40bb-b6b7-cd3882220681';
