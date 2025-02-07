INSERT INTO public.subsidy_stage_transition_messages (id, subsidy_stage_transition_id, version, status, subject, content_html, content_pdf, created_at, updated_at) VALUES ('b135a0f1-c584-4f69-bbad-e9db91a0de6d', '3063cb42-5d55-4b9b-82e6-6250a4481296', 1, 'published', 'Aanvraag afgekeurd', e'{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>
    <p>&nbsp;</p>

    <h2>Besluit</h2>
    <p>Uw subsidieaanvraag moet ik helaas afwijzen. De reden(en) licht ik hieronder toe.</p>
    <p>&nbsp;</p>

    {if $content->stage2->firstAssessmentRejectedNote}
        <h2>Motivering bij het besluit</h2>
        <p>Uw aanvraag is beoordeeld op grond van:
        <ul>
            <li>Kaderregeling subsidies OCW, SZW en VWS;</li>
            <li>Subsidieregeling opleidingsactiviteiten Arts Internationale Gezondheid en Tropengeneeskunde 2021-2026;</li>
            <li>Algemene wet bestuursrecht, in het bijzonder titel 4.2 Subsidies;</li>
            <li>Profielregister RGS en opleidingsregister OIGT.</li>
        </ul>
        Uw aanvraag voldoet niet aan de volgende voorwaarde(n):</p>
        <p>{$content->stage2->firstAssessmentRejectedNote|breakLines}</p>
        <p>&nbsp;</p>
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
        <img class="signature" alt="handtekening" src="{$content->getSignature(\'vws_dusi_signature.jpg\')|dataStream}" />
        <br/>
        L. van der Weij
    </p>
{/block}
', e'{layout \'letter_layout.latte\'}

{block concern}
    Betreft: Afwijzing aanvraag {$content->subsidyTitle}
{/block}

{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>
    <p>&nbsp;</p>

    <h2>Besluit</h2>
    <p>Uw subsidieaanvraag moet ik helaas afwijzen. De reden(en) licht ik hieronder toe.</p>
    <p>&nbsp;</p>

    {if $content->stage2->firstAssessmentRejectedNote}
        <h2>Motivering bij het besluit</h2>
        <p>Uw aanvraag is beoordeeld op grond van:
            <ul>
                <li>Kaderregeling subsidies OCW, SZW en VWS;</li>
                <li>Subsidieregeling opleidingsactiviteiten Arts Internationale Gezondheid en Tropengeneeskunde 2021-2026;</li>
                <li>Algemene wet bestuursrecht, in het bijzonder titel 4.2 Subsidies;</li>
                <li>Profielregister RGS en opleidingsregister OIGT.</li>
            </ul>
            Uw aanvraag voldoet niet aan de volgende voorwaarde(n):</p>
        <p>{$content->stage2->firstAssessmentRejectedNote|breakLines}</p>
        <p>&nbsp;</p>
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
        <img class="signature" alt="handtekening" src="{$content->getSignature(\'vws_dusi_signature.jpg\')|dataStream}" />
        <br/>
        L. van der Weij
    </p>
{/block}

{block sidebar}
    {include parent}
{/block}
', '2024-02-07 13:20:34', null);


INSERT INTO public.subsidy_stage_transition_messages (id, subsidy_stage_transition_id, version, status, subject, content_html, content_pdf, created_at, updated_at) VALUES ('ef41a929-6556-4dec-975e-5d75f5a48a64', '9fc7740b-1951-4e99-8f89-5608bb0e3a0b', 1, 'published', 'Aanvraag goedgekeurd', e'{block content}
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
        referentienummer Dossiernummer.</p>

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
', e'{layout \'letter_layout.latte\'}

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
        referentienummer Dossiernummer.</p>

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
', '2024-02-07 13:20:34', null);
