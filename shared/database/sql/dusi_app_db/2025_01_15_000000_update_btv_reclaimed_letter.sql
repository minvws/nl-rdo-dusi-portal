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
