<?php
/** @noinspection PhpReturnValueOfMethodIsNeverUsedInspection, PhpUnusedPrivateMethodInspection, SpellCheckingInspection, PhpSameParameterValueInspection, PhpNamedArgumentsWithChangedOrderInspection */

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\ComparisonCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\Operator;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\OrCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\DataRetentionPeriod;
use MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\Traits\CreateField;
use function Amp\Iterator\toArray;

class PCZMApplicationFieldsTableSeeder extends Seeder
{
    use CreateField;

    public const SUBSIDY_STAGE_HASH_BANK_ACCOUNT_DUPLICATES_UUID = 'bd26ae6f-05ac-4690-81da-87b534f7758d';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->pczmApplicationFields();
        $this->pczmSubsidyStageHashes();
    }

    public function pczmApplicationFields(): void
    {
        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'firstName',
            title: 'Voornaam',
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'infix',
            title: 'Tussenvoegsel',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'lastName',
            title: 'Achternaam',
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createDateField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'dateOfBirth',
            title: 'Geboortedatum',
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'street',
            title: 'Straat',
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'houseNumber',
            title: 'Huisnummer',
            inputMode: 'numeric',
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'houseNumberSuffix',
            title: 'Huisnummer toevoeging',
            maxLength: 10,
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createPostalCodeField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'postalCode',
            title: 'Postcode',
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'city',
            title: 'Plaats',
            maxLength: 100,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createCountryField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'country',
            title: 'Land',
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'phoneNumber',
            title: 'Telefoonnummer',
            inputMode: 'tel',
            maxLength: 20,
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'email',
            title: 'E-mailadres',
            inputMode: 'email',
            maxLength: 300,
            isRequired: false,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createBankAccountField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'bankAccountNumber',
            title: 'IBAN',
            retentionPeriod: DataRetentionPeriod::Long
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'bankAccountHolder',
            title: 'Naam rekeninghouder',
            maxLength: 50,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createUploadField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'certifiedEmploymentDocument',
            title: 'Gewaarmerkt verzekeringsbericht',
            mimeTypes: ['image/jpeg', 'image/png', 'application/pdf'],
            maxFileSize: 20971520,
            minItems: 1,
            maxItems: 20,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createUploadField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'wiaDecisionDocument',
            title: 'WIA-Beslissing',
            mimeTypes: ['image/jpeg', 'image/png', 'application/pdf'],
            maxFileSize: 20971520,
            minItems: 1,
            maxItems: 20,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'isWiaDecisionPostponed',
            title: 'Is WIA beslissing uitgesteld?',
            options: ['Ja', 'Nee'],
            retentionPeriod: DataRetentionPeriod::Short
        );

        //If isWiaDecisionPostponed === yes
        $this->createUploadField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'wiaDecisionPostponedLetter',
            title: 'Toekenningsbrief',
            mimeTypes: ['image/jpeg', 'image/png', 'application/pdf'],
            maxFileSize: 20971520,
            minItems: 1,
            maxItems: 20,
            requiredCondition: new ComparisonCondition(
                1, 'isWiaDecisionPostponed', Operator::Identical, 'Ja'
            ),
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createUploadField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'employmentContract',
            title: 'Bewijs dienstverband',
            mimeTypes: ['image/jpeg', 'image/png', 'application/pdf'],
            maxFileSize: 20971520,
            minItems: 1,
            maxItems: 20,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'employmentFunction',
            title: 'Functie',
            options: [
                'Ambulancechauffeur',
                'Anesthesiemedewerker en/of operatieassistent',
                'Arts',
                'Bachelor medisch hulpverlener',
                'Doktersassistent',
                'Helpende',
                'Physician assistant',
                'Praktijkondersteuner huisarts',
                'Verpleegkundig specialist',
                '(gespecialiseerd) Verpleegkundige',
                'Verzorgende in de individuele gezondheidszorg (VIG’er) of verzorgende',
                'Zorgondersteuner en/of voedingsassistent',
                'Anders',
            ],
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'otherEmploymentFunction',
            title: 'Andere functie',
            maxLength: 300,
            requiredCondition: new ComparisonCondition(1, 'employmentFunction', Operator::Identical, 'Anders'),
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'employerKind',
            title: 'Werkgever',
            options: ['Zorgaanbieder', 'Andere organisatie'],
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createUploadField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'otherEmployerDeclarationFile',
            title: 'Verklaring zorgaanbieder',
            mimeTypes: ['image/jpeg', 'image/png', 'application/pdf'],
            maxFileSize: 20971520,
            minItems: 1,
            maxItems: 20,
            requiredCondition: new OrCondition([
                new ComparisonCondition(1, 'employmentFunction', Operator::Identical, 'Anders'),
                new ComparisonCondition(1, 'employerKind', Operator::Identical, 'Andere organisatie'),
            ]),
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'hasBeenWorkingAtJudicialInstitution',
            title: 'Bent u werkzaam geweest bij een justitiële inrichting?',
            options: ['Ja', 'Nee'],
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'BIGNumberJudicialInstitution',
            title: 'BIG-nummer',
            maxLength: 11,
            requiredCondition: new ComparisonCondition(1, 'hasBeenWorkingAtJudicialInstitution', Operator::Identical, 'Ja'),
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createUploadField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'socialMedicalAssessment',
            title: 'Medisch onderzoeksverslag',
            mimeTypes: ['image/jpeg', 'image/png', 'application/pdf'],
            maxFileSize: 20971520,
            minItems: 1,
            maxItems: 20,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'hasPostCovidDiagnose',
            title: 'Heeft langdurige post-COVID klachten',
            options: ['Ja', 'Nee'],
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createUploadField(
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
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
            subsidyStageId: SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            code: 'truthfullyCompleted',
            title: '',
            retentionPeriod: DataRetentionPeriod::Short
        );
    }

    private function pczmSubsidyStageHashes(): void
    {
        DB::table('subsidy_stage_hashes')->insert([
            'id' => self::SUBSIDY_STAGE_HASH_BANK_ACCOUNT_DUPLICATES_UUID,
            'subsidy_stage_id' => SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID,
            'name' => 'Bank account',
            'description' => 'Bank account duplicate reporting',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        /** @var Field $bankAccountNumber */
        $bankAccountNumber = DB::table('fields')
            ->where('subsidy_stage_id', SubsidyStagesTableSeeder::PCZM_STAGE_1_UUID)
            ->where('code', 'bankAccountNumber')
            ->where('title', 'IBAN')
            ->first();

        DB::table('subsidy_stage_hash_fields')->insert([
          'subsidy_stage_hash_id' => self::SUBSIDY_STAGE_HASH_BANK_ACCOUNT_DUPLICATES_UUID,
          'field_id' => $bankAccountNumber->id,
        ]);
    }
}
