INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id,
                           required_condition, retention_period_on_approval, exclude_from_clone_data)
VALUES ('67b97372-77ae-4ede-b8b5-57c68db29926', 'Controlevragen', null, 'multiselect',
        '{"options": ["Valt de aanvrager onder de WSNP/bewindvoering?", "Alle benodigde documenten zijn aangeleverd", "De aanvraag kan verleend worden", "Het IBAN is juist vermeld in het Portaal en in de verplichting in SAP", "De verplichting is juist in SAP geboekt", "De verplichting is in SAP goedgekeurd", "De verleningsbeschikking mag verzonden worden"]}',
        false, 'internalAssessmentChecklist', 'user', '0838f8a9-b2ff-4669-9d42-1c51a1134a34',
        null, 'short', false);

UPDATE public.subsidy_stage_uis
SET input_ui = '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Checklist","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/internalAssessmentChecklist","options":{"format":"checkbox-group"}}]}]},{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessorMotivatedValid","options":{"format":"checkbox"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/internalAssessment","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/internalAssessmentInternalNote","options":{"format":"textarea"}}]}]}]}',
    view_ui = '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessorMotivatedValid","options":{"readonly":true}},{"type":"CustomControl","scope":"#\/properties\/internalAssessment","options":{"readonly":true,"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/internalAssessmentInternalNote","options":{"readonly":true,"format":"textarea"}}]}'
where id = '8f7b2a5f-050e-4dd2-9d05-4e1d20f3929a';

INSERT INTO public.subsidy_stage_transition_messages (id, subsidy_stage_transition_id, version, status, subject,
                                                      content_html, content_pdf, created_at, updated_at)
VALUES ('be7c6a5e-24a5-44d2-8e13-f259651e72e0', '6a4d09fe-c648-45d3-b404-beaa95cb1013', 1, 'published',
        'Aanvraag vastgesteld', e'{block content}
    <p>Beste lezer,</p>
    <p>Met mijn brief van {$content->stage4->createdAt|date:"d-m-Y"} heb ik u een subsidie verleend van
        {$content->stage2->amount} voor het onderdeel "Buitenland" van de opleiding AIGT. Uit de aan mij ter beschikking
        gestelde gegevens blijkt dat u inmiddels het opleidingsonderdeel "Buitenland" van de opleiding AIGT heeft afgerond.
    </p>
    <p>&nbsp;</p>

    <h2>Besluit</h2>
    <p>Ik stel hierbij de subsidie vast op {$content->stage4->amount}.</p>

    <h2>Motivering bij het besluit</h2>
    <p>De subsidie is gebaseerd op artikel 1.2 van de Kaderregeling subsidies OCW, SZW en VWS en de Subsidieregeling
        opleidingsactiviteiten Arts Internationale Gezondheid en Tropengeneeskunde 2021-2026.
        Dit is een projectsubsidie als bedoeld in artikel 1.5, onder a 1° van de Kaderregeling subsidies OCW, SZW en
        VWS.
        De subsidie wordt eenmalig verstrekt ter compensatie van de kosten voor het volgen van het opleidingsonderdeel
        “Buitenland’ van de opleiding tot Arts Internationale Gezondheid en Tropengeneeskunde.
    </p>

    <p><ins>Wet- en regelgeving</ins><br />
        De subsidie is vastgesteld op grond van:
        <ul>
            <li>Kaderregeling subsidies OCW, SZW en VWS;</li>
            <li>Subsidieregeling opleidingsactiviteiten Arts Internationale Gezondheid en Tropengeneeskunde 2021-2026;</li>
            <li>Algemene wet bestuursrecht, in het bijzonder titel 4.2 Subsidies;</li>
            <li>Profielregister RGS en opleidingsregister OIGT.</li>
        </ul>
    </p>

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
        <img class="signature" alt="handtekening" src="{$content->getSignature(\'vws_dusi_signature.jpg\')|dataStream}" />
        <br/>
        L. van der Weij
    </p>
{/block}
', e'{layout \'letter_layout.latte\'}

{block concern}
    Betreft: Vaststelling subsidie {$content->subsidyTitle}
{/block}

{block content}
    <p>Beste lezer,</p>
    <p>Met mijn brief van {$content->stage4->createdAt|date:"d-m-Y"} heb ik u een subsidie verleend van
        {$content->stage2->amount} voor het onderdeel "Buitenland" van de opleiding AIGT. Uit de aan mij ter beschikking
        gestelde gegevens blijkt dat u inmiddels het opleidingsonderdeel "Buitenland" van de opleiding AIGT heeft afgerond.
    </p>
    <p>&nbsp;</p>

    <h2>Besluit</h2>
    <p>Ik stel hierbij de subsidie vast op {$content->stage4->amount}.</p>

    <h2>Motivering bij het besluit</h2>
    <p>De subsidie is gebaseerd op artikel 1.2 van de Kaderregeling subsidies OCW, SZW en VWS en de Subsidieregeling
        opleidingsactiviteiten Arts Internationale Gezondheid en Tropengeneeskunde 2021-2026.
        Dit is een projectsubsidie als bedoeld in artikel 1.5, onder a 1° van de Kaderregeling subsidies OCW, SZW en
        VWS.
        De subsidie wordt eenmalig verstrekt ter compensatie van de kosten voor het volgen van het opleidingsonderdeel
        “Buitenland’ van de opleiding tot Arts Internationale Gezondheid en Tropengeneeskunde.
    </p>

    <p><ins>Wet- en regelgeving</ins><br />
        De subsidie is vastgesteld op grond van:
        <ul>
            <li>Kaderregeling subsidies OCW, SZW en VWS;</li>
            <li>Subsidieregeling opleidingsactiviteiten Arts Internationale Gezondheid en Tropengeneeskunde 2021-2026;</li>
            <li>Algemene wet bestuursrecht, in het bijzonder titel 4.2 Subsidies;</li>
            <li>Profielregister RGS en opleidingsregister OIGT.</li>
        </ul>
    </p>

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
        <img class="signature" alt="handtekening" src="{$content->getSignature(\'vws_dusi_signature.jpg\')|dataStream}" />
        <br/>
        L. van der Weij
    </p>
{/block}

{block sidebar}
    {include parent}
{/block}
', '2024-03-13 15:10:31', null);

INSERT INTO public.subsidy_stage_transition_messages (id, subsidy_stage_transition_id, version, status, subject, content_html, content_pdf, created_at, updated_at) VALUES ('b8cfccbc-d9ba-463f-8cbc-4930057a0dff', '66d64304-b165-4ada-9cf0-eb28b2772e47', 1, 'published', 'Vordering aanvraag', e'{block content}
    <p>Beste lezer,</p>
    <p>Met mijn brief van <Beschikkingsdatum> heb ik u een subsidie verleend van {$content->stage2->amount} voor het
            onderdeel "Buitenland" van de opleiding AIGT. Uit de aan mij ter beschikking gestelde gegevens blijkt dat u
            inmiddels het opleidingsonderdeel "Buitenland" van de opleiding AIGT heeft afgerond.
    </p>
    <p>
        Met mijn vrief van  {$content->stage4->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.
        Met deze brief beslis ik op uw aanvraag.
    </p>
    <p>&nbsp;</p>

    <h2>Besluit</h2>
    <p>Ik stel hierbij de subsidie vast op {$content->stage4->amount}.</p>

    <h2>Motivering bij het besluit</h2>
    <p>De subsidie is gebaseerd op artikel 1.2 van de Kaderregeling subsidies OCW, SZW en VWS en de Subsidieregeling opleidingsactiviteiten Arts Internationale Gezondheid en Tropengeneeskunde 2021-2026.
        Dit is een projectsubsidie als bedoeld in artikel 1.5, onder a 1° van de Kaderregeling subsidies OCW, SZW en VWS.
        De subsidie wordt eenmalig verstrekt ter compensatie van de kosten voor het volgen van het opleidingsonderdeel “Buitenland’ van de opleiding tot Arts Internationale Gezondheid en Tropengeneeskunde.
    </p>

    <p><ins>Wet- en regelgeving</ins><br />
        De subsidie is vastgesteld op grond van:
        <ul>
            <li>Kaderregeling subsidies OCW, SZW en VWS;</li>
            <li>Subsidieregeling opleidingsactiviteiten Arts Internationale Gezondheid en Tropengeneeskunde 2021-2026;</li>
            <li>Algemene wet bestuursrecht, in het bijzonder titel 4.2 Subsidies;</li>
            <li>Profielregister RGS en opleidingsregister OIGT.</li>
        </ul>
    </p>

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
        <img class="signature" alt="handtekening" src="{$content->getSignature(\'vws_dusi_signature.jpg\')|dataStream}" />
        <br/>
        L. van der Weij
    </p>
{/block}
', e'{layout \'letter_layout.latte\'}

{block concern}
    Betreft: Vordering subsidie {$content->subsidyTitle}
{/block}

{block content}
    <p>Beste lezer,</p>
    <p>Met mijn brief van {$content->stage4->createdAt|date:"d-m-Y"} heb ik u een subsidie verleend van
        {$content->stage2->amount} voor het onderdeel "Buitenland" van de opleiding AIGT. Uit de aan mij ter beschikking
        gestelde gegevens blijkt dat u inmiddels het opleidingsonderdeel "Buitenland" van de opleiding AIGT heeft afgerond.
    </p>
    <p>&nbsp;</p>

    <h2>Besluit</h2>
    <p>Ik stel hierbij de subsidie vast op {$content->stage4->amount}.</p>

    <h2>Motivering bij het besluit</h2>
    <p>De subsidie is gebaseerd op artikel 1.2 van de Kaderregeling subsidies OCW, SZW en VWS en de Subsidieregeling
        opleidingsactiviteiten Arts Internationale Gezondheid en Tropengeneeskunde 2021-2026.
        Dit is een projectsubsidie als bedoeld in artikel 1.5, onder a 1° van de Kaderregeling subsidies OCW, SZW en
        VWS.
        De subsidie wordt eenmalig verstrekt ter compensatie van de kosten voor het volgen van het opleidingsonderdeel
        “Buitenland’ van de opleiding tot Arts Internationale Gezondheid en Tropengeneeskunde.
    </p>

    <p><ins>Wet- en regelgeving</ins><br />
        De subsidie is vastgesteld op grond van:
        <ul>
            <li>Kaderregeling subsidies OCW, SZW en VWS;</li>
            <li>Subsidieregeling opleidingsactiviteiten Arts Internationale Gezondheid en Tropengeneeskunde 2021-2026;</li>
            <li>Algemene wet bestuursrecht, in het bijzonder titel 4.2 Subsidies;</li>
            <li>Profielregister RGS en opleidingsregister OIGT.</li>
        </ul>
    </p>

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
        <img class="signature" alt="handtekening" src="{$content->getSignature(\'vws_dusi_signature.jpg\')|dataStream}" />
        <br/>
        L. van der Weij
    </p>
{/block}
{block sidebar}
    {include parent}
{/block}
', '2024-03-13 15:10:31', null);

UPDATE public.subsidy_stage_transition_messages SET content_html = e'{block content}
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
', content_pdf = e'{layout \'letter_layout.latte\'}

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
' WHERE id = 'b135a0f1-c584-4f69-bbad-e9db91a0de6d';

UPDATE public.subsidy_stage_transition_messages SET content_html = e'{block content}
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

', content_pdf = e'{layout \'letter_layout.latte\'}

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
' WHERE id = 'ef41a929-6556-4dec-975e-5d75f5a48a64';
