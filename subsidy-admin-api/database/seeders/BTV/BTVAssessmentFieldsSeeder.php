<?php

declare(strict_types=1);

/** @noinspection PhpReturnValueOfMethodIsNeverUsedInspection, PhpUnusedPrivateMethodInspection, SpellCheckingInspection, PhpSameParameterValueInspection, PhpNamedArgumentsWithChangedOrderInspection */

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\BTV;

use Illuminate\Database\Seeder;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\ComparisonCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\Operator;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\DataRetentionPeriod;
use MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\Traits\CreateField;

class BTVAssessmentFieldsSeeder extends Seeder
{
    use CreateField;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->firstAssessmentFields();
        $this->secondAssessmentFields();
        $this->internalAssessmentFields();
        $this->assignationDelayPeriodFields();
        $this->assignationAssessmentFields();
        $this->assignationAuditAssessmentFields();
        $this->assignationImplementationAssessmentFields();
    }

    public function firstAssessmentFields(): void
    {
        // Eerste beoordeling
        $this->createMultiSelectField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_2_UUID,
            code:           'firstAssessmentChecklist',
            title:          'Gecontroleerd',
            options:        [
                                'De aanvrager valt niet onder de WSNP/bewindvoering',
                                'De aanvrager heeft niet eerder een BTV-subsidieaanvraag ingediend',
                                'De aanvrager komt niet voor in het M&O register',
                                'De persoonsgegevens zijn door de aanvrager juist ingevuld (NAW-gegevens, IBAN)',
                                'Uittreksel van het BRP is opgestuurd (< 12 maanden)',
                                'De aanvrager is een ingezetene (> 4 maanden) in Nederland',
                                'De aanvrager is ouder dan 18 jaar',
                                'De ingevoerde persoonsgegevens zijn conform het BRP uittreksel',
                                'De medische verklaringen zijn volledig ingevuld en op naam van de aanvrager',
                                'De verklaring van de arts over het behandeltraject is minder dan 2 maanden oud',
                                'De verklaring van de arts over het behandeltraject is ondertekend en voorzien van een naamstempel',
                                'Het opgegeven BIG-nummer komt overeen met het BIG-register',
                                'De aanvrager heeft genderdysforie',
                                'De aanvrager heeft minimaal een jaar voor de aanvraag hormoonbehandeling ondergaan, of is hiermee vanwege medische redenen gestopt of kon deze om medische redenen niet ondergaan',
                                'De verklaring van de arts met de vermelding van het type behandeling is opgestuurd (<12 maanden oud)',
                                'De verklaring van de arts met de vermelding van de type behandeling is ondertekend en voorzien van een naamstempel',
                                'De type behandeling voldoet aan de voorwaarden conform de subsidieregeling',
                                'Het opgegeven IBAN is correct',
                                'De verificatiebevestiging met betrekking tot de verklaring over het behandeltraject is ontvangen',
                                'De verificatiebevestiging met betrekking tot de verklaring over het type behandeling is ontvangen',
                            ],
            isRequired: false,
        );

        $this->createTextField(
            subsidyStageId:  BTVSubsidyStagesSeeder::BTV_STAGE_2_UUID,
            code:            'businessPartnerNumber',
            title:           'Zakenpartnernummer',
            inputMode:       'numeric',
            params:          ['maxLength' => 20],
            isRequired:      false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createSelectField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_2_UUID,
            code:           'amount',
            title:          'Bedrag',
            options:        ['€ 3.830', '€ 13.720'],
            requiredCondition: new ComparisonCondition(2, 'firstAssessment', Operator::Identical, 'Goedgekeurd'),
            isRequired:     false,
            excludeFromCloneData: true
        );

        $this->createSelectField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_2_UUID,
            code:           'firstAssessment',
            title:          'Beoordeling',
            options:        ['Aanvulling nodig', 'Afgekeurd', 'Goedgekeurd']
        );

        //Toelichting
        $this->createSelectField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_2_UUID,
            code: 'firstAssessmentRequestedComplementReason',
            title: 'Reden',
            options: ['Incomplete aanvraag', 'Onduidelijkheid of vervolgvragen'],
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_2_UUID,
            code: 'firstAssessmentRequestedComplementNote',
            title: 'Toelichting van benodigde aanvullingen',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_2_UUID,
            code: 'firstAssessmentRejectedNote',
            title: 'Reden van afkeuring',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_2_UUID,
            code: 'firstAssessmentApprovedNote',
            title: 'Motivatie van goedkeuring',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createUploadField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_2_UUID,
            code:           'firstAssessmentAdditionalDocuments',
            title:          'Aanvullende informatie behandelaar',
            isRequired:     false,
            mimeTypes:      ['image/jpeg', 'image/png', 'application/pdf'],
            maxFileSize:    20971520,
            minItems:       1,
            maxItems:       20
        );

        $this->createTextField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_2_UUID,
            code: 'firstAssessmentInternalNote',
            title: 'Interne notitie',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );
    }

    public function secondAssessmentFields(): void
    {
        //Tweede beoordeling
        $this->createCheckboxField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_3_UUID,
            code: 'firstAssessorMotivatedValid',
            title: 'De motivatie van de eerste behandelaar is duidelijk en correct',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createSelectField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_3_UUID,
            code: 'secondAssessment',
            title: 'Beoordeling',
            options: ['Eens met de eerste beoordeling', 'Oneens met de eerste beoordeling'],
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createUploadField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_3_UUID,
            code:           'secondAssessmentAdditionalDocuments',
            title:          'Aanvullende informatie behandelaar',
            isRequired:     false,
            mimeTypes:      ['image/jpeg', 'image/png', 'application/pdf'],
            maxFileSize:    20971520,
            minItems:       1,
            maxItems:       20
        );

        $this->createTextField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_3_UUID,
            code: 'secondAssessmentInternalNote',
            title: 'Interne notitie',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );
    }

    public function internalAssessmentFields(): void
    {
        //Interne controle
        $this->createMultiSelectField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_4_UUID,
            code: 'internalAssessmentChecklist',
            title: 'Controlevragen',
            options: [
                'Aanvrager valt niet onder de WSNP/bewindvoering?',
                'Alle benodigde documenten zijn aangeleverd',
                'Het subsidiebedrag klopt met de gekozen behandeling',
                'De aanvraag kan verleend worden',
                'Het subsidiebedrag is juist vermeld in het Portaal en in de verplichting in SAP',
                'Het IBAN is juist vermeld in het Portaal en in de verplichting in SAP',
                'De verplichting is juist in SAP geboekt',
                'De verplichting is in SAP goedgekeurd',
                'De verleningsbeschikking mag verzonden worden',
            ],
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );


        $this->createSelectField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_4_UUID,
            code: 'internalAssessment',
            title: 'Beoordeling',
            options: ['Eens met de eerste beoordeling', 'Oneens met de eerste beoordeling'],
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_4_UUID,
            code: 'internalAssessmentInternalNote',
            title: 'Interne notitie',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );
    }

    public function assignationDelayPeriodFields(): void
    {
        $this->createMultiSelectField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_5_UUID,
            code: 'assessmentDelayChecklist',
            title: 'Controlevragen',
            options: [
                'De aanvraag zit in de steekproef',
                'De aanvraag zit niet in de steekproef',
                'De opgevraagde informatie is compleet en akkoord',
                'De vaststelling is geaccordeerd in SAP',
                'Het dossier is compleet (voltoets)',
            ],
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createSelectField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_5_UUID,
            code: 'assessment',
            title: 'Beoordeling',
            options: ['Vaststellen', 'Vorderen', 'Uitstellen'],
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId:  BTVSubsidyStagesSeeder::BTV_STAGE_5_UUID,
            code:            'motivation',
            title:           'Motivering',
            isRequired:      false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId:  BTVSubsidyStagesSeeder::BTV_STAGE_5_UUID,
            code:            'newAssignationAmount',
            inputMode:      'float',
            title:           'Vastgesteld bedrag',
            isRequired:      false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId:  BTVSubsidyStagesSeeder::BTV_STAGE_5_UUID,
            code:            'reclaimAmount',
            inputMode:      'float',
            title:           'Te vorderen bedrag',
            isRequired:      false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_5_UUID,
            code: 'internalNote',
            title: 'Interne notitie',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createUploadField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_5_UUID,
            code:           'proof',
            title:          'Bewijsstukken',
            mimeTypes:      ['image/jpeg', 'image/png', 'application/pdf'],
            isRequired:     false,
            minItems:       1,
            maxItems:       20,
            maxFileSize:    20971520
        );
    }

    public function assignationAssessmentFields(): void
    {
        $this->createMultiSelectField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_6_UUID,
            code: 'assessmentDelayChecklist',
            title: 'Controlevragen',
            options: [
                'De aanvraag zit in de steekproef',
                'De aanvraag zit niet in de steekproef',
                'De opgevraagde informatie is compleet en akkoord',
                'De vaststelling is geaccordeerd in SAP',
                'Het dossier is compleet (voltoets)',
            ],
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createSelectField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_6_UUID,
            code: 'assessment',
            title: 'Beoordeling',
            options: ['Vaststellen', 'Vorderen', 'Uitstellen'],
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId:  BTVSubsidyStagesSeeder::BTV_STAGE_6_UUID,
            code:            'motivation',
            title:           'Motivering',
            isRequired:      false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId:  BTVSubsidyStagesSeeder::BTV_STAGE_6_UUID,
            code:            'newAssignationAmount',
            inputMode:      'float',
            title:           'Vastgesteld bedrag',
            isRequired:      false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId:  BTVSubsidyStagesSeeder::BTV_STAGE_6_UUID,
            code:            'reclaimAmount',
            inputMode:      'float',
            title:           'Te vorderen bedrag',
            isRequired:      false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_6_UUID,
            code: 'internalNote',
            title: 'Interne notitie',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createUploadField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_6_UUID,
            code:           'proof',
            title:          'Bewijsstukken',
            isRequired:     false,
            mimeTypes:      ['image/jpeg', 'image/png', 'application/pdf'],
            maxFileSize:    20971520,
            minItems:       1,
            maxItems:       20
        );
    }

    public function assignationAuditAssessmentFields(): void
    {

        $this->createMultiSelectField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_7_UUID,
            code: 'assignationAuditChecklist',
            title: 'Controlevragen',
            options: [
                'De subsidie mag worden vastgesteld',
                'De vaststelling is geaccordeerd in SAP',
                'Het vorderingsnummer is aangemaakt in SAP (alleen bij terugvorderen)',
                'De beschikking mag verzonden worden'
            ],
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createSelectField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_7_UUID,
            code: 'assessment',
            title: 'Beoordeling',
            options: ['Eens met de beoordeling op de vaststelling', 'Oneens met de beoordeling op de vaststelling'],
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId:  BTVSubsidyStagesSeeder::BTV_STAGE_7_UUID,
            code:            'reclaimNumber',
            title:           'Vorderingsnummer',
            isRequired:      false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_7_UUID,
            code: 'internalNote',
            title: 'Interne notitie',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );
    }

    public function assignationImplementationAssessmentFields(): void
    {
        $this->createSelectField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_8_UUID,
            code: 'assessment',
            title: 'Beoordeling',
            options: ['Eens met de beoordeling op de vaststelling', 'Oneens met de beoordeling op de vaststelling'],
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: BTVSubsidyStagesSeeder::BTV_STAGE_8_UUID,
            code: 'internalNote',
            title: 'Interne notitie',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );
    }
}
