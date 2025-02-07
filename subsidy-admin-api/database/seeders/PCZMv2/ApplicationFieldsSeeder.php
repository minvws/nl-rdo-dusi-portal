<?php

declare(strict_types=1);

/** @noinspection PhpReturnValueOfMethodIsNeverUsedInspection, PhpUnusedPrivateMethodInspection, SpellCheckingInspection, PhpSameParameterValueInspection, PhpNamedArgumentsWithChangedOrderInspection */

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\PCZMv2;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\ComparisonCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\Operator;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\OrCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\DataRetentionPeriod;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\Traits\CreateField;

class ApplicationFieldsSeeder extends Seeder
{
    use CreateField;

    public const SUBSIDY_STAGE_HASH_BANK_ACCOUNT_DUPLICATES_UUID = 'ea1bcfad-3cc8-440b-bf65-617f224856a2';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createApplicationFields();
        $this->pczmSubsidyStageHashes();
    }

    public function createApplicationFields(): void
    {
        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::STAGE_1_UUID,
            code: 'firstName',
            title: 'Voornaam',
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::STAGE_1_UUID,
            code: 'infix',
            title: 'Tussenvoegsel',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::STAGE_1_UUID,
            code: 'lastName',
            title: 'Achternaam',
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createDateField(
            subsidyStageId: SubsidyStagesSeeder::STAGE_1_UUID,
            code: 'dateOfBirth',
            title: 'Geboortedatum',
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::STAGE_1_UUID,
            code: 'street',
            title: 'Straat',
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::STAGE_1_UUID,
            code: 'houseNumber',
            title: 'Huisnummer',
            inputMode: 'numeric',
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::STAGE_1_UUID,
            code: 'houseNumberSuffix',
            title: 'Huisnummer toevoeging',
            params: ['maxLength' => 10],
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createPostalCodeField(
            subsidyStageId: SubsidyStagesSeeder::STAGE_1_UUID,
            code: 'postalCode',
            title: 'Postcode',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::STAGE_1_UUID,
            code: 'city',
            title: 'Plaats',
            params: ['maxLength' => 100],
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createCountryField(
            subsidyStageId: SubsidyStagesSeeder::STAGE_1_UUID,
            code: 'country',
            title: 'Land',
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::STAGE_1_UUID,
            code: 'phoneNumber',
            title: 'Telefoonnummer',
            inputMode: 'tel',
            params: ['maxLength' => 20],
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::STAGE_1_UUID,
            code: 'email',
            title: 'E-mailadres',
            inputMode: 'email',
            params: ['maxLength' => 300],
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createBankAccountField(
            subsidyStageId: SubsidyStagesSeeder::STAGE_1_UUID,
            code: 'bankAccountNumber',
            title: 'IBAN',
            retentionPeriod: DataRetentionPeriod::Long
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::STAGE_1_UUID,
            code: 'bankAccountHolder',
            title: 'Naam rekeninghouder',
            params: ['maxLength' => 50],
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createUploadField(
            subsidyStageId: SubsidyStagesSeeder::STAGE_1_UUID,
            code: 'bankStatement',
            title: 'Bankafschrift',
            isRequired: false,
            mimeTypes: ['image/jpeg', 'image/png', 'application/pdf'],
            maxFileSize: 20971520,
            minItems: 1,
            maxItems: 20,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createUploadField(
            subsidyStageId: SubsidyStagesSeeder::STAGE_1_UUID,
            code: 'certifiedEmploymentDocument',
            title: 'Gewaarmerkt verzekeringsbericht',
            mimeTypes: ['image/jpeg', 'image/png', 'application/pdf'],
            maxFileSize: 20971520,
            minItems: 1,
            maxItems: 20,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createUploadField(
            subsidyStageId: SubsidyStagesSeeder::STAGE_1_UUID,
            code: 'wiaDecisionDocument',
            title: 'WIA-Beslissing',
            mimeTypes: ['image/jpeg', 'image/png', 'application/pdf'],
            maxFileSize: 20971520,
            minItems: 1,
            maxItems: 20,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesSeeder::STAGE_1_UUID,
            code: 'isWiaDecisionPostponed',
            title: 'Is WIA beslissing uitgesteld?',
            options: ['Ja', 'Nee'],
            retentionPeriod: DataRetentionPeriod::Short
        );

        //If isWiaDecisionPostponed === yes
        $this->createUploadField(
            subsidyStageId: SubsidyStagesSeeder::STAGE_1_UUID,
            code: 'wiaDecisionPostponedLetter',
            title: 'Toekenningsbrief',
            mimeTypes: ['image/jpeg', 'image/png', 'application/pdf'],
            maxFileSize: 20971520,
            minItems: 1,
            maxItems: 20,
            requiredCondition: new ComparisonCondition(
                stage: 1,
                fieldCode: 'isWiaDecisionPostponed',
                operator: Operator::Identical,
                value: 'Ja'
            ),
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesSeeder::STAGE_1_UUID,
            code: 'isWiaDecisionFirstSickDayOutsidePeriod',
            title: 'Blijkt uit uw WIA-beslissing dat uw eerste ziektedag buiten de periode van 1 maart 2020 tot en met 31 december 2020 ligt?',
            options: ['Ja', 'Nee'],
            retentionPeriod: DataRetentionPeriod::Short
        );

        //If isWiaDecisionFirstSickDayOutsidePeriod === yes
        $this->createUploadField(
            subsidyStageId: SubsidyStagesSeeder::STAGE_1_UUID,
            code: 'wiaFirstSickDayOutsidePeriodProof',
            title: 'Onderbouwing eerste ziektedag buiten de periode',
            description: 'Upload een document waarmee u kunt aantonen dat uw langdurige Post-COVID klachten toch het gevolg zijn van een vermoedelijke COVID-19 besmetting in de periode van 1 maart 2020 tot en met 31 december 2020. U kunt dit bijvoorbeeld onderbouwen met een verklaring van uw (bedrijfs)arts. Toegestane bestandstypen: pdf, jpg, jpeg, png. Maximale bestandsgrootte: 20 MB.',
            mimeTypes: ['image/jpeg', 'image/png', 'application/pdf'],
            maxFileSize: 20971520,
            minItems: 1,
            maxItems: 20,
            requiredCondition: new ComparisonCondition(
                stage: 1,
                fieldCode: 'isWiaDecisionFirstSickDayOutsidePeriod',
                operator: Operator::Identical,
                value: 'Ja'
            ),
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createUploadField(
            subsidyStageId: SubsidyStagesSeeder::STAGE_1_UUID,
            code: 'employmentContract',
            title: 'Bewijs dienstverband',
            mimeTypes: ['image/jpeg', 'image/png', 'application/pdf'],
            maxFileSize: 20971520,
            minItems: 1,
            maxItems: 20,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesSeeder::STAGE_1_UUID,
            code: 'employmentFunction',
            title: 'Functie',
            options: [
                'Ambulancechauffeur/(zorgambulance)begeleider',
                'Anesthesiemedewerker en/of operatieassistent',
                'Arts',
                'Bachelor medisch hulpverlener',
                'Doktersassistent',
                'Helpende',
                'Physician assistant',
                'Praktijkondersteuner huisarts',
                'Verpleegkundig specialist',
                '(gespecialiseerd) Verpleegkundige',
                'Verzorgende in de individuele gezondheidszorg (VIG’er) of (kraam)verzorgende',
                'ADL-assistent, zorgondersteuner en/of voedingsassistent',
                'Begeleider gehandicaptenzorg, jeugdzorg en/of psychiatrische inrichting',
                'GGD-test medewerker',
                'Anders',
            ],
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::STAGE_1_UUID,
            code: 'otherEmploymentFunction',
            title: 'Andere functie',
            params: ['maxLength' => 300],
            requiredCondition: new ComparisonCondition(1, 'employmentFunction', Operator::Identical, 'Anders'),
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesSeeder::STAGE_1_UUID,
            code: 'employerKind',
            title: 'Werkgever',
            options: ['Zorgaanbieder', 'Andere organisatie'],
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createUploadField(
            subsidyStageId: SubsidyStagesSeeder::STAGE_1_UUID,
            code: 'otherEmployerDeclarationFile',
            title: 'Verklaring zorgaanbieder',
            mimeTypes: ['image/jpeg', 'image/png', 'application/pdf'],
            maxFileSize: 20971520,
            minItems: 1,
            maxItems: 20,
            requiredCondition: new OrCondition(
                [
                    new ComparisonCondition(
                        stage: 1,
                        fieldCode: 'employmentFunction',
                        operator: Operator::Identical,
                        value: 'Anders'
                    ),
                    new ComparisonCondition(
                        stage: 1,
                        fieldCode: 'employerKind',
                        operator: Operator::Identical,
                        value: 'Andere organisatie'
                    ),
                ]
            ),
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesSeeder::STAGE_1_UUID,
            code: 'hasBeenWorkingAtJudicialInstitution',
            title: 'Bent u werkzaam geweest bij een justitiële inrichting?',
            options: ['Ja', 'Nee'],
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::STAGE_1_UUID,
            code: 'BIGNumberJudicialInstitution',
            title: 'BIG-nummer',
            params: ['maxLength' => 11],
            requiredCondition: new ComparisonCondition(
                stage: 1,
                fieldCode: 'hasBeenWorkingAtJudicialInstitution',
                operator: Operator::Identical,
                value: 'Ja'
            ),
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createUploadField(
            subsidyStageId: SubsidyStagesSeeder::STAGE_1_UUID,
            code: 'socialMedicalAssessment',
            title: 'Sociaal-medische beoordeling (medische rapportage)',
            mimeTypes: ['image/jpeg', 'image/png', 'application/pdf'],
            maxFileSize: 20971520,
            minItems: 1,
            maxItems: 20,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesSeeder::STAGE_1_UUID,
            code: 'hasPostCovidDiagnose',
            title: 'Heeft langdurige post-COVID klachten',
            options: ['Ja', 'Nee'],
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createUploadField(
            subsidyStageId: SubsidyStagesSeeder::STAGE_1_UUID,
            code: 'doctorsCertificate',
            title: 'Verklaring arts',
            mimeTypes: ['image/jpeg', 'image/png', 'application/pdf'],
            maxFileSize: 20971520,
            minItems: 1,
            maxItems: 20,
            requiredCondition: new ComparisonCondition(1, 'hasPostCovidDiagnose', Operator::Identical, 'Nee'),
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createCheckboxField(
            subsidyStageId: SubsidyStagesSeeder::STAGE_1_UUID,
            code: 'truthfullyCompleted',
            title: '',
            retentionPeriod: DataRetentionPeriod::Short,
            excludeFromCloneData: true
        );
    }

    private function pczmSubsidyStageHashes(): void
    {
        DB::table('subsidy_stage_hashes')->insert([
            'id' => self::SUBSIDY_STAGE_HASH_BANK_ACCOUNT_DUPLICATES_UUID,
            'subsidy_stage_id' => SubsidyStagesSeeder::STAGE_1_UUID,
            'name' => 'Bank account (v2)',
            'description' => 'Bank account duplicate reporting (version 2)',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        /** @var Field $bankAccountNumber */
        $bankAccountNumber = DB::table('fields')
            ->where('subsidy_stage_id', SubsidyStagesSeeder::STAGE_1_UUID)
            ->where('code', 'bankAccountNumber')
            ->where('title', 'IBAN')
            ->first();

        DB::table('subsidy_stage_hash_fields')->insert([
            'subsidy_stage_hash_id' => self::SUBSIDY_STAGE_HASH_BANK_ACCOUNT_DUPLICATES_UUID,
            'field_id' => $bankAccountNumber->id,
        ]);
    }
}
