INSERT INTO public.fields (id, title, description, type,
                           params, is_required, code, source, subsidy_stage_id,
                           required_condition, retention_period_on_approval, exclude_from_clone_data)
VALUES ('105120eb-fe79-4e50-b5a0-c94ddef4ab94', 'Motivatie van goedkeuring', null, 'text',
        '{
            "maxLength": null
        }', false, 'firstAssessmentApprovedNote', 'user', '7075fcad-7d92-42f6-b46c-7733869019e0',
        null, 'short', false);

UPDATE public.fields
SET code = 'internalNote'
WHERE code = 'implementationCoordinatorAssessmentInternalNote'
  AND subsidy_stage_id = 'e5da8f2e-db87-45df-8967-ea3dceb2b207';

UPDATE public.subsidy_stage_transition_messages
SET content_html = e'{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>
    <p>&nbsp;</p>

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

    <p><ins>Wet- en regelgeving</ins><br/>
    Op deze subsidie zijn de volgende wet- en regelgeving en registers van toepassing:
    <ul>
        <li>Kaderregeling subsidies OCW, SZW en VWS;</li>
        <li>Subsidieregeling opleidingsactiviteiten Arts Internationale Gezondheid en Tropengeneeskunde 2021-2026;</li>
        <li>Algemene wet bestuursrecht, in het bijzonder titel 4.2 Subsidies;</li>
        <li>Profielregister RGS en opleidingsregister OIGT.</li>
    </ul>
    </p>

    <p><ins>Meldingsplicht</ins><br />
    U bent verplicht een melding te maken bij de Dienst Uitvoering Subsidies aan Instellingen (DUS-I) wanneer niet,
        niet op tijd of niet geheel zal worden voldaan aan de verplichtingen die aan de subsidie zijn verbonden.</p>

    <p><ins>Wat als u zich niet aan de voorschriften houdt?</ins><br>
        Het niet voldoen aan de verplichtingen kan tot gevolg hebben dat u de subsidie helemaal of gedeeltelijk moet terugbetalen.</p>

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
        <img class="signature" alt="handtekening" src="{$content->getSignature(\'vws_dusi_signature.jpg\')|dataStream}" />
        <br/>
        L. van der Weij
    </p>
{/block}

',
    content_pdf = e'{layout \'letter_layout.latte\'}

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

    <p><ins>Wet- en regelgeving</ins><br/>
        Op deze subsidie zijn de volgende wet- en regelgeving en registers van toepassing:
    <ul>
        <li>Kaderregeling subsidies OCW, SZW en VWS;</li>
        <li>Subsidieregeling opleidingsactiviteiten Arts Internationale Gezondheid en Tropengeneeskunde 2021-2026;</li>
        <li>Algemene wet bestuursrecht, in het bijzonder titel 4.2 Subsidies;</li>
        <li>Profielregister RGS en opleidingsregister OIGT.</li>
    </ul>
</p>

    <p><ins>Meldingsplicht</ins><br />
        U bent verplicht een melding te maken bij de Dienst Uitvoering Subsidies aan Instellingen (DUS-I) wanneer niet,
        niet op tijd of niet geheel zal worden voldaan aan de verplichtingen die aan de subsidie zijn verbonden.</p>

    <p><ins>Wat als u zich niet aan de voorschriften houdt?</ins><br>
        Het niet voldoen aan de verplichtingen kan tot gevolg hebben dat u de subsidie helemaal of gedeeltelijk moet terugbetalen.</p>

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
        <img class="signature" alt="handtekening" src="{$content->getSignature(\'vws_dusi_signature.jpg\')|dataStream}" />
        <br/>
        L. van der Weij
    </p>
{/block}

{block sidebar}
    {include parent}
{/block}
', updated_at = 'now()'
WHERE id = 'ef41a929-6556-4dec-975e-5d75f5a48a64';
