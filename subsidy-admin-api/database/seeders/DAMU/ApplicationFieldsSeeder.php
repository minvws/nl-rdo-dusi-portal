<?php

declare(strict_types=1);

/** @noinspection PhpReturnValueOfMethodIsNeverUsedInspection, PhpUnusedPrivateMethodInspection, SpellCheckingInspection, PhpSameParameterValueInspection, PhpNamedArgumentsWithChangedOrderInspection */

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\DAMU;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\ComparisonCondition;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\Operator;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\DataRetentionPeriod;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldSource;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\Traits\CreateField;

class ApplicationFieldsSeeder extends Seeder
{
    use CreateField;

    public const SUBSIDY_STAGE_HASH_BANK_ACCOUNT_DUPLICATES_UUID = '7f16ecd5-0450-4424-87d2-6626ee3bccda';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createFields();
        $this->createSubsidyStageHashes();
    }

    public function createFields(): void
    {
        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'firstName',
            title:          'Voornaam',
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'infix',
            title:          'Tussenvoegsel',
            isRequired:     false,
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'lastName',
            title:          'Achternaam',
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'street',
            title:          'Straatnaam',
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'houseNumber',
            title:          'Huisnummer',
            inputMode:      'numeric',
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'houseNumberSuffix',
            title:          'Huisnummer toevoeging',
            params:         ['maxLength' => 10],
            isRequired:     false,
        );

        $this->createPostalCodeField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'postalCode',
            title:          'Postcode',
            isRequired:     false,
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'city',
            title:          'Plaatsnaam',
            params:         ['maxLength' => 100],
        );

        $this->createCountryField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'country',
            title:          'Land',
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'phoneNumber',
            title:          'Telefoonnummer',
            inputMode:      'tel',
            params:         ['maxLength' => 20],
            isRequired:     false,
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'email',
            title:          'E-mailadres',
            inputMode:      'email',
            params:         ['maxLength' => 300],
            isRequired:     false,
        );

        $this->createBankAccountField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'bankAccountNumber',
            title:          'IBAN',
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'bankAccountHolder',
            title:          'Naam rekeninghouder',
            params:         ['maxLength' => 50],
        );

        $this->createUploadField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code: 'bankStatement',
            title: 'Bankafschrift',
            isRequired: false,
            mimeTypes: ['image/jpeg', 'image/png', 'application/pdf'],
            maxFileSize: 20971520,
            minItems: 1,
            maxItems: 20,
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'isSingleParentFamily',
            title:          'Is er sprake van een eenoudergezin?',
            options:        ['Ja', 'Nee'],
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'hasAlimony',
            title:          'Ontvangt u kinderalimentatie?',
            options:        ['Ja', 'Nee'],
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'annualIncomeParentA',
            title:          'Jaarinkomen ouder 1',
            inputMode:      'numeric',
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'annualIncomeParentB',
            title:          'Jaarinkomen ouder 2',
            inputMode:      'numeric',
            isRequired:     false,
            requiredCondition: new ComparisonCondition(1, 'isSingleParentFamily', Operator::Identical, 'Nee'),
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'annualJointIncome',
            title:          'Jaarinkomen totaal',
            inputMode:      'numeric',
            params:         ['readonly' => true],
            isRequired:     false,
            source:         FieldSource::Calculated,
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'childName',
            title:          'Naam kind',
        );

        $this->createDateField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code: 'dateOfBirth',
            title: 'Geboortedatum',
            retentionPeriod: DataRetentionPeriod::Short
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'residentialStreet',
            title:          'Straatnaam',
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'residentialHouseNumber',
            title:          'Huisnummer',
            inputMode:      'numeric',
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'residentialHouseNumberSuffix',
            title:          'Huisnummer toevoeging',
            params:         ['maxLength' => 10],
            isRequired:     false,
        );

        $this->createPostalCodeField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'residentialPostalCode',
            title:          'Postcode',
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'residentialCity',
            title:          'Plaatsnaam',
            params:         ['maxLength' => 100],
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'educationType',
            title:          'Gaat naar het',
            options:        ['Primair onderwijs', 'Voortgezet onderwijs'],
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'damuSchoolPrimary',
            title:          'DAMU-school',
            options:        ['Amsterdam - Olympiaschool', 'Den Haag - School voor Jong Talent', 'Rotterdam - Nieuwe Park Rozenburgschool'],
            isRequired:     false,
            requiredCondition: new ComparisonCondition(1, 'educationType', Operator::Identical, 'Primair onderwijs'),
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'damuSchoolSecondary',
            title:          'DAMU-school',
            options:        [
                'Amsterdam - Gerrit van der Veen College',
                'Amsterdam - Individueel Voortgezet Kunstzinnig Onderwijs (IVKO)',
                'Arnhem - Beekdal Lyceum',
                'Den Haag - Interfaculteit School voor Jong Talent',
                'Enschede - Het Stedelijk Lyceum, locatie Kottenpark',
                'Haren - Zernike College',
                'Maastricht - Bonnefanten College',
                'Rotterdam - Havo/Vwo voor muziek en dans',
                'Rotterdam - Thorbecke Voortgezet Onderwijs',
                'Tilburg - Koning Willem II College',
                'Venlo - Valuas College',
            ],
            isRequired: false,
            requiredCondition: new ComparisonCondition(1, 'educationType', Operator::Identical, 'Voortgezet onderwijs'),
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'damuSchoolAddress',
            title:          'DAMU-school adres',
            isRequired:     false,
            source:         FieldSource::Calculated,
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'hboPreviousEducationPrimary',
            title:          'HBO vooropleiding',
            options:        [
                'Academie voor Theater en Dans: Nationale Balletacademie (dans) (21QA)',
                'Codarts (dans) (14NI)',
                'Koninklijk Conservatorium (dans en muziek) (23KJ)]'
            ],
            isRequired:     false,
            requiredCondition: new ComparisonCondition(1, 'educationType', Operator::Identical, 'Primair onderwijs'),
        );

        $this->createSelectField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'hboPreviousEducationSecondary',
            title:          'HBO vooropleiding',
            options:        [
                'Academie voor Theater en Dans 5 o’clock class (dans) (21QA)',
                'Academie voor Theater en Dans: Nationale Balletacademie (dans) (21QA)',
                'ArtEz (dans) (27NF)',
                'Codarts (dans) (14NI)',
                'Codarts (dans en muziek) (14NI)',
                'Conservatorium Maastricht (muziek) (25JX)',
                'Conservatorium van Amsterdam (Muziek) (21QA)',
                'Fontys Hogeschool voor de Kunsten (dans) (30GB)',
                'Fontys Hogeschool voor de Kunsten (musical) (30GB)',
                'Koninklijk Conservatorium (dans en muziek) (23KJ)',
                'Lucia Marthas Institute for Performing Arts (dans) (25LW)',
                'Prins Claus Conservatorium (muziek) (25BE)'
            ],
            isRequired: false,
            requiredCondition: new ComparisonCondition(1, 'educationType', Operator::Identical, 'Voortgezet onderwijs'),
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'travelDistanceSingleTrip',
            title:          'Reisafstand enkele reis (in kilometers)',
            inputMode:      'float',
            params:         ['minimum' => 1, 'maximum' => 9999],
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'travelExpenseReimbursement',
            title:          'Vergoeding per kilometer',
            inputMode:      'float',
            params:         ['readonly' => true],
            isRequired:     false,
            source:         FieldSource::Calculated,
        );

        $this->createTextField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'requestedSubsidyAmount',
            title:          'Gevraagd subsidiebedrag',
            inputMode:      'float',
            params:         ['readonly' => true],
            isRequired:     false,
            source:         FieldSource::Calculated,
        );

        $this->createTextAreaField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'explanation',
            title:          'Eventuele toelichting op uw aanvraag',
            isRequired:     false,
        );

        $this->createUploadField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'IBDocument',
            title:          'IB60 formulier',
            mimeTypes:      ['image/jpeg', 'image/png', 'application/pdf'],
            minItems:       1,
            maxItems:       20,
            maxFileSize:    20971520
        );

        $this->createUploadField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'proofOfRegistrationDAMUSchool',
            title:          'Inschrijfbewijs DAMU-school',
            mimeTypes:      ['image/jpeg', 'image/png', 'application/pdf'],
            minItems:       1,
            maxItems:       20,
            maxFileSize:    20971520
        );

        $this->createUploadField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'proofOfRegistrationHboCollaborationPartner',
            title:          'Inschrijfbewijs hbo-vooropleiding dans en muziek',
            mimeTypes:      ['image/jpeg', 'image/png', 'application/pdf'],
            minItems:       1,
            maxItems:       20,
            maxFileSize:    20971520
        );

        $this->createCheckboxField(
            subsidyStageId: SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            code:           'truthfullyCompleted',
            title:          '',
            excludeFromCloneData: true
        );

    }

    private function createSubsidyStageHashes(): void
    {
        DB::table('subsidy_stage_hashes')->insert([
            'id' => self::SUBSIDY_STAGE_HASH_BANK_ACCOUNT_DUPLICATES_UUID,
            'subsidy_stage_id' => SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID,
            'name' => 'Bank account',
            'description' => 'Bank account duplicate reporting',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        /** @var Field $bankAccountNumber */
        $bankAccountNumber = DB::table('fields')
            ->where('subsidy_stage_id', SubsidyStagesSeeder::SUBSIDY_STAGE_1_UUID)
            ->where('code', 'bankAccountNumber')
            ->where('title', 'IBAN')
            ->first();

        DB::table('subsidy_stage_hash_fields')->insert([
            'subsidy_stage_hash_id' => self::SUBSIDY_STAGE_HASH_BANK_ACCOUNT_DUPLICATES_UUID,
            'field_id' => $bankAccountNumber->id,
        ]);
    }
}
