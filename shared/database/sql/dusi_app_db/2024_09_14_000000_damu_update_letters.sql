-- Update DAMU message SubsidyStageTransitionsSeeder::TRANSITION_STAGE_4_TO_APPROVED
-- TRANSITION_STAGE_4_TO_APPROVED_MESSAGE

UPDATE public.subsidy_stage_transition_messages
SET content_html = '{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling ''{$content->subsidyTitle}'' met referentienummer {$content->reference}.
    </p>

    <h2>Besluit</h2>
    <p>Hierbij ken ik uw aanvraag (gedeeltelijk) toe en stel ik de subsidie (aangepast) vast
        op € {formatCurrency($content->stage3->amount)}. U vroeg een subsidie aan
        van € {formatCurrency($content->stage1->requestedSubsidyAmount)}.</p>

    {if $content->stage1->educationType === ''Primair onderwijs''}
    <p>De subsidie is toegekend op grond van artikel 3 van de Regeling en beoordeeld aan de hand van de criteria uit artikel
        6 en 7 van de Regeling.</p>
    {/if}

    {if $content->stage1->educationType === ''Voortgezet onderwijs''}
    <p>De subsidie is toegekend op grond van artikel 2 van de Regeling en beoordeeld aan de hand van de criteria uit artikel
        3 en 6 van de Regeling.</p>
    {/if}

    {if $content->stage2->firstAssessmentApprovedNote}
    <h2>Motivering bij het besluit</h2>
    <p>{$content->stage2->firstAssessmentApprovedNote|breakLines}</p>
    {/if}

    <h2>Waaraan moet u voldoen?</h2>
    <p>U moet voldoen aan de verplichtingen in de wet- en regelgeving in deze beschikking.</p>

    <u>Wet- en regelgeving</u>
    <p>Op deze subsidie is de volgende wet- en regelgeving van toepassing:
    <ul>
        <li>Wet overige OCW subsidies;</li>
        <li>Kaderregeling subsidies OCW, SZW en VWS;</li>
        <li>Subsidieregeling reiskosten DAMU-leerlingen, nr.24900503;</li>
        <li>Algemene wet bestuursrecht;</li>
        <li>Wet bestuurlijke boete bijzondere meldingsplichten die gelden voor subsidies die door ministers zijn
            verleend.
        </li>
    </ul>
    </p>
    <p>De regelgeving kunt u raadplegen via <a href="https://wetten.overheid.nl" target="_blank">wetten.overheid.nl</a>.</p>

    <u>Meldingsplicht</u>
    <p>U bent verplicht een melding te maken bij de Dienst Uitvoering Subsidies aan Instellingen (DUS-I) wanneer niet, niet
        op tijd of niet geheel zal worden voldaan aan de verplichtingen die aan de subsidie zijn verbonden. U doet in ieder
        geval melding als de leerling dit subsidiejaar met de opleiding stopt.</p>

    <u>Verantwoording</u>
    <p>De subsidie is direct vastgesteld. Dit betekent dat er na afloop van het subsidiejaar geen verantwoording van de
        subsidie nodig is.</p>

    <u>Wat als u zich niet aan de voorschriften houdt?</u>
    <p>Het niet voldoen aan de verplichtingen kan tot gevolg hebben dat u de subsidie helemaal of gedeeltelijk moet
        terugbetalen.</p>

    <h2>Wanneer ontvangt u de subsidie?</h2>
    <p>Ik streef ernaar het toegekende subsidiebedrag zo spoedig mogelijk naar u over te maken onder vermelding van
        het referentienummer {$content->reference}.</p>
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de minister van Onderwijs, Cultuur en Wetenschap,<br/>
        namens deze,<br/>
        afdelingshoofd Dienst Uitvoering Subsidies aan Instellingen,<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        J. Kraaijeveld
    </p>
{/block}

{block objectionFooter}
    <footer>
        <h2>Bezwaar</h2>
        <p>Als u belang hebt bij dit besluit, dan kunt u hiertegen binnen 6 weken, gerekend vanaf de verzenddatum, bezwaar
            maken. Stuur uw bezwaarschrift naar DUO, Postbus 30205, 2500 GE Den Haag. U kunt uw bezwaar ook digitaal
            indienen via <a href="https://www.duo.nl/zakelijk/oneens-met-duo/bezwaar-maken.jsp" target="_blank">www.duo.nl/zakelijk/oneens-met-duo/bezwaar-maken.jsp</a>.
        </p>
    </footer>
{/block}
',
    content_pdf = '{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling ''{$content->subsidyTitle}'' met referentienummer {$content->reference}.
    </p>

    <h2>Besluit</h2>
    <p>Hierbij ken ik uw aanvraag (gedeeltelijk) toe en stel ik de subsidie (aangepast) vast
        op € {formatCurrency($content->stage3->amount)}. U vroeg een subsidie aan
        van € {formatCurrency($content->stage1->requestedSubsidyAmount)}.</p>

    {if $content->stage1->educationType === ''Primair onderwijs''}
    <p>De subsidie is toegekend op grond van artikel 3 van de Regeling en beoordeeld aan de hand van de criteria uit artikel
        6 en 7 van de Regeling.</p>
    {/if}

    {if $content->stage1->educationType === ''Voortgezet onderwijs''}
    <p>De subsidie is toegekend op grond van artikel 2 van de Regeling en beoordeeld aan de hand van de criteria uit artikel
        3 en 6 van de Regeling.</p>
    {/if}

    {if $content->stage2->firstAssessmentApprovedNote}
    <h2>Motivering bij het besluit</h2>
    <p>{$content->stage2->firstAssessmentApprovedNote|breakLines}</p>
    {/if}

    <h2>Waaraan moet u voldoen?</h2>
    <p>U moet voldoen aan de verplichtingen in de wet- en regelgeving in deze beschikking.</p>

    <u>Wet- en regelgeving</u>
    <p>Op deze subsidie is de volgende wet- en regelgeving van toepassing:
    <ul>
        <li>Wet overige OCW subsidies;</li>
        <li>Kaderregeling subsidies OCW, SZW en VWS;</li>
        <li>Subsidieregeling reiskosten DAMU-leerlingen, nr.24900503;</li>
        <li>Algemene wet bestuursrecht;</li>
        <li>Wet bestuurlijke boete bijzondere meldingsplichten die gelden voor subsidies die door ministers zijn
            verleend.
        </li>
    </ul>
    </p>
    <p>De regelgeving kunt u raadplegen via <a href="https://wetten.overheid.nl" target="_blank">wetten.overheid.nl</a>.</p>

    <u>Meldingsplicht</u>
    <p>U bent verplicht een melding te maken bij de Dienst Uitvoering Subsidies aan Instellingen (DUS-I) wanneer niet, niet
        op tijd of niet geheel zal worden voldaan aan de verplichtingen die aan de subsidie zijn verbonden. U doet in ieder
        geval melding als de leerling dit subsidiejaar met de opleiding stopt.</p>

    <u>Verantwoording</u>
    <p>De subsidie is direct vastgesteld. Dit betekent dat er na afloop van het subsidiejaar geen verantwoording van de
        subsidie nodig is.</p>

    <u>Wat als u zich niet aan de voorschriften houdt?</u>
    <p>Het niet voldoen aan de verplichtingen kan tot gevolg hebben dat u de subsidie helemaal of gedeeltelijk moet
        terugbetalen.</p>

    <h2>Wanneer ontvangt u de subsidie?</h2>
    <p>Ik streef ernaar het toegekende subsidiebedrag zo spoedig mogelijk naar u over te maken onder vermelding van
        het referentienummer {$content->reference}.</p>
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de minister van Onderwijs, Cultuur en Wetenschap,<br/>
        namens deze,<br/>
        afdelingshoofd Dienst Uitvoering Subsidies aan Instellingen,<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        J. Kraaijeveld
    </p>
{/block}

{block objectionFooter}
    <footer>
        <h2>Bezwaar</h2>
        <p>Als u belang hebt bij dit besluit, dan kunt u hiertegen binnen 6 weken, gerekend vanaf de verzenddatum, bezwaar
            maken. Stuur uw bezwaarschrift naar DUO, Postbus 30205, 2500 GE Den Haag. U kunt uw bezwaar ook digitaal
            indienen via <a href="https://www.duo.nl/zakelijk/oneens-met-duo/bezwaar-maken.jsp" target="_blank">www.duo.nl/zakelijk/oneens-met-duo/bezwaar-maken.jsp</a>.
        </p>
    </footer>
{/block}
',
    updated_at = 'now()'
WHERE id = '9445db1e-2aeb-4434-be02-e57622c28e77';


-- Update DAMU message SubsidyStageTransitionsSeeder::TRANSITION_STAGE_3_TO_REJECTED
-- TRANSITION_STAGE_3_TO_REJECTED_MESSAGE

UPDATE public.subsidy_stage_transition_messages
SET content_html = '{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling ''{$content->subsidyTitle}'' met referentienummer {$content->reference}.
    </p>

    <h2>Besluit</h2>
    <p>Uw aanvraag moet ik helaas afwijzen. De reden(en) licht ik hieronder toe.</p>
    <p>&nbsp;</p>

    <h2>Motivatie</h2>

    {if $content->stage1->educationType === ''Primair onderwijs''}
    <p>Uw aanvraag is beoordeeld aan de hand van de criteria uit artikel 6 en 7 van de Regeling.</p>
    {/if}

    {if $content->stage1->educationType === ''Voortgezet onderwijs''}
    <p>Uw aanvraag is beoordeeld aan de hand van de criteria uit artikel 3 en 6 van de Regeling.</p>
    {/if}

    {if $content->stage2->firstAssessmentRejectedNote}
    <p>Uw aanvraag voldoet niet aan de volgende voorwaarde(n):</p>
    <p>{$content->stage2->firstAssessmentRejectedNote|breakLines}</p>
    {/if}
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de minister van Onderwijs, Cultuur en Wetenschap,<br/>
        namens deze,<br/>
        afdelingshoofd Dienst Uitvoering Subsidies aan Instellingen,<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        J. Kraaijeveld
    </p>
{/block}

{block objectionFooter}
<footer>
    <h2>Bezwaar</h2>
    <p>Als u belang hebt bij dit besluit, dan kunt u hiertegen binnen 6 weken, gerekend vanaf de verzenddatum, bezwaar
        maken. Stuur uw bezwaarschrift naar DUO, Postbus 30205, 2500 GE Den Haag. U kunt uw bezwaar ook digitaal
        indienen via <a href="https://www.duo.nl/zakelijk/oneens-met-duo/bezwaar-maken.jsp" target="_blank">www.duo.nl/zakelijk/oneens-met-duo/bezwaar-maken.jsp</a>.
    </p>
</footer>
{/block}

',
    content_pdf = '{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->submittedAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling ''{$content->subsidyTitle}'' met referentienummer {$content->reference}.
    </p>

    <h2>Besluit</h2>
    <p>Uw aanvraag moet ik helaas afwijzen. De reden(en) licht ik hieronder toe.</p>
    <p>&nbsp;</p>

    <h2>Motivatie</h2>

    {if $content->stage1->educationType === ''Primair onderwijs''}
    <p>Uw aanvraag is beoordeeld aan de hand van de criteria uit artikel 6 en 7 van de Regeling.</p>
    {/if}

    {if $content->stage1->educationType === ''Voortgezet onderwijs''}
    <p>Uw aanvraag is beoordeeld aan de hand van de criteria uit artikel 3 en 6 van de Regeling.</p>
    {/if}

    {if $content->stage2->firstAssessmentRejectedNote}
    <p>Uw aanvraag voldoet niet aan de volgende voorwaarde(n):</p>
    <p>{$content->stage2->firstAssessmentRejectedNote|breakLines}</p>
    {/if}
{/block}

{block signature}
    <p>
        Met vriendelijke groet,<br/>
        <br/>
        de minister van Onderwijs, Cultuur en Wetenschap,<br/>
        namens deze,<br/>
        afdelingshoofd Dienst Uitvoering Subsidies aan Instellingen,<br/>
        <br/>
        <img class="signature" alt="handtekening" src="{$content->getSignature(''vws_dusi_signature.jpg'')|dataStream}" />
        <br/>
        J. Kraaijeveld
    </p>
{/block}

{block objectionFooter}
<footer>
    <h2>Bezwaar</h2>
    <p>Als u belang hebt bij dit besluit, dan kunt u hiertegen binnen 6 weken, gerekend vanaf de verzenddatum, bezwaar
        maken. Stuur uw bezwaarschrift naar DUO, Postbus 30205, 2500 GE Den Haag. U kunt uw bezwaar ook digitaal
        indienen via <a href="https://www.duo.nl/zakelijk/oneens-met-duo/bezwaar-maken.jsp" target="_blank">www.duo.nl/zakelijk/oneens-met-duo/bezwaar-maken.jsp</a>.
    </p>
</footer>
{/block}

',
    updated_at = 'now()'
WHERE id = '350d6eae-0f5e-49aa-9c80-280bcc6efafb';
