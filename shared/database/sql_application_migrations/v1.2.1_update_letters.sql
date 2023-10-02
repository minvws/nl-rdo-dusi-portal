BEGIN;

UPDATE public.subsidy_stage_transition_messages
    SET content_html = '{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.
    </p>

    {if $content->stage2->firstAssessmentRequestedComplementReason === 'Incomplete aanvraag'}
        <p>Uw aanvraag bevat helaas nog onvoldoende gegevens om een beslissing te kunnen nemen over afhandeling.</p>
        <p>&nbsp;</p>

        <h2>Wat moet u aanvullen?</h2>
    {else}
       <p>Om de aanvraag goed te kunnen beoordelen ontvang ik graag van u de volgende aanvullende informatie:</p>
    {/if}

    <p>{$content->stage2->firstAssessmentRequestedComplementNote}</p>
    <p>&nbsp;</p>

    <h2>Termijn</h2>
    <p>
        Ik ontvang de informatie graag binnen 2 weken na dagtekening van deze brief, dus uiterlijk {$content->createdAt->addDays(14)|date:"d-m-Y"}.
        U kunt deze toevoegen aan uw aanvraag door het door u ingevulde aanvraagformulier te heropenen in Mijn DUS-I.
    </p>
    <p>
        Als de gevraagde gegevens niet binnen de gestelde termijn van 2 weken door mij zijn ontvangen, of onvoldoende zijn
        voor de beoordeling, kan ik niet op tijd een besluit nemen op uw aanvraag.
    </p>
    <p>&nbsp;</p>
{/block}
',
    content_pdf = '{layout ''letter_layout.latte''}

{block concern}
    Betreft: Vragen over aanvraag {$content->subsidyTitle}
{/block}

{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.
    </p>

    {if $content->stage2->firstAssessmentRequestedComplementReason === ''Incomplete aanvraag''}
        <p>Uw aanvraag bevat helaas nog onvoldoende gegevens om een beslissing te kunnen nemen over afhandeling.</p>
        <p>&nbsp;</p>

        <h2>Wat moet u aanvullen?</h2>
    {else}
       <p>Om de aanvraag goed te kunnen beoordelen ontvang ik graag van u de volgende aanvullende informatie:</p>
    {/if}

    <p>
        {$content->stage2->firstAssessmentRequestedComplementNote}
    </p>
    <p>&nbsp;</p>

    <h2>Termijn</h2>
    <p>
        Ik ontvang de informatie graag binnen 2 weken na dagtekening van deze brief, dus uiterlijk {$content->createdAt->addDays(14)|date:"d-m-Y"}.
        U kunt deze toevoegen aan uw aanvraag door het door u ingevulde aanvraagformulier te heropenen in Mijn DUS-I.
    </p>
    <p>
        Als de gevraagde gegevens niet binnen de gestelde termijn van 2 weken door mij zijn ontvangen, of onvoldoende zijn voor de beoordeling,
        kan ik niet op tijd een besluit nemen op uw aanvraag.
    </p>
    <p>&nbsp;</p>
{/block}

{block sidebar}
    {include parent}
{/block}
'
    WHERE id = '85bf054e-c6e3-42d2-880d-07c29d0fe6bf';

UPDATE public.subsidy_stage_transition_messages
    SET content_html = '{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>
    <p>&nbsp;</p>

    <h2>Besluit</h2>
    <p>Uw aanvraag moet ik helaas afwijzen. De reden(en) licht ik hieronder toe.</p>
    <p>&nbsp;</p>

    {if $content->stage2->firstAssessmentRejectedNote}
        <h2>Motivering bij het besluit</h2>
        <p>Uw aanvraag wordt afgewezen vanwege de volgende reden(en):</p>
        <p>{$content->stage2->firstAssessmentRejectedNote}</p>
        <p>&nbsp;</p>
    {/if}
{/block}
',
    content_pdf = '{layout ''letter_layout.latte''}

{block concern}
    Betreft: Afwijzing aanvraag {$content->subsidyTitle}
{/block}

{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>
    <p>&nbsp;</p>

    <h2>Besluit</h2>
    <p>Uw aanvraag moet ik helaas afwijzen. De reden(en) licht ik hieronder toe.</p>
    <p>&nbsp;</p>

    {if $content->stage2->firstAssessmentRejectedNote}
        <h2>Motivering bij het besluit</h2>
        <p>Uw aanvraag wordt afgewezen vanwege de volgende reden(en):</p>
        <p>{$content->stage2->firstAssessmentRejectedNote}</p>
        <p>&nbsp;</p>
    {/if}
{/block}

{block sidebar}
    {include parent}
{/block}
'
    WHERE id = '64a636d8-ed0c-4bb6-982e-f948c68755b6';

UPDATE public.subsidy_stage_transition_messages
    SET content_html = '{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>
    <p>&nbsp;</p>

    <h2>Besluit</h2>
    <p>Uw aanvraag moet ik helaas afwijzen. De reden(en) licht ik hieronder toe.</p>
    <p>&nbsp;</p>

    {if $content->stage2->firstAssessmentRejectedNote}
        <h2>Motivering bij het besluit</h2>
        <p>Uw aanvraag wordt afgewezen vanwege de volgende reden(en):</p>
        <p>{$content->stage2->firstAssessmentRejectedNote}</p>
        <p>&nbsp;</p>
    {/if}
{/block}
',
    content_pdf = '{layout ''letter_layout.latte''}

{block concern}
    Betreft: Afwijzing aanvraag {$content->subsidyTitle}
{/block}

{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>
    <p>&nbsp;</p>

    <h2>Besluit</h2>
    <p>Uw aanvraag moet ik helaas afwijzen. De reden(en) licht ik hieronder toe.</p>
    <p>&nbsp;</p>

    {if $content->stage2->firstAssessmentRejectedNote}
        <h2>Motivering bij het besluit</h2>
        <p>Uw aanvraag wordt afgewezen vanwege de volgende reden(en):</p>
        <p>{$content->stage2->firstAssessmentRejectedNote}</p>
        <p>&nbsp;</p>
    {/if}
{/block}

{block sidebar}
    {include parent}
{/block}
'
    WHERE id = '7da32b2f-4f0d-44ab-bc87-07718db4bfd5';

UPDATE public.subsidy_stage_transition_messages
    SET content_html = '{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>
    <p>&nbsp;</p>

    <h2>Besluit</h2>
    <p>Hierbij verleen ik u de financiële tegemoetkoming/subsidie van {$content->stage2->amount}.</p>
    <p>&nbsp;</p>
{/block}
',
    content_pdf = '{layout 'letter_layout.latte'}

{block concern}
    Betreft: Verlening aanvraag {$content->subsidyTitle}
{/block}

{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>
    <p>&nbsp;</p>

    <h2>Besluit</h2>
    <p>Hierbij verleen ik u de financiële tegemoetkoming/subsidie van {$content->stage2->amount}.</p>
    <p>&nbsp;</p>
{/block}

{block sidebar}
    {include parent}
{/block}
'
    WHERE id = '9c2ad81e-cf52-41a3-966f-fc9757de15c9';


UPDATE public.subsidy_stage_transitions
 SET "condition" = '{"type":"or","conditions":[{"type":"and","conditions":[{"type":"comparison","stage":2,"fieldCode":"firstAssessment","operator":"===","value":"Afgekeurd"},{"type":"comparison","stage":4,"fieldCode":"internalAssessment","operator":"===","value":"Goedgekeurd"}]},{"type":"and","conditions":[{"type":"comparison","stage":2,"fieldCode":"firstAssessment","operator":"===","value":"Goedgekeurd"},{"type":"comparison","stage":4,"fieldCode":"internalAssessment","operator":"===","value":"Afgekeurd"}]}]}'
 WHERE id = '005a5acb-a908-44d2-8b69-a50d5ef43870';

COMMIT;
