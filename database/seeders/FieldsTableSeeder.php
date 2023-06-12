<?php
/** @noinspection PhpReturnValueOfMethodIsNeverUsedInspection, PhpUnusedPrivateMethodInspection, SpellCheckingInspection, PhpSameParameterValueInspection, PhpNamedArgumentsWithChangedOrderInspection */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class FieldsTableSeeder extends Seeder
{
    private function createField(
        string $formId,
        string $label,
        string $type,
        int $sort,
        ?string $description = null,
        ?array $params = null,
        bool $isRequired = true
    ): string {
        $id = Uuid::uuid4();

        DB::table('fields')->insert([
            'id' => $id,
            'form_id' => $formId,
            'label' => $label,
            'description' => $description,
            'type' => $type,
            'params' => json_encode($params),
            'is_required' => $isRequired,
            'sort' => $sort
        ]);

        return $id;
    }

    private function createTextField(
        string $formId,
        string $label,
        int $sort,
        ?string $description = null,
        ?string $inputMode = null,
        ?int $maxLength = null,
        bool $isRequired = true
    ): string {
        return $this->createField(
            formId: $formId,
            label: $label,
            description: $description,
            type: $inputMode !== null ? "text:$inputMode" : 'text',
            params: ['maxLength' => $maxLength],
            isRequired: $isRequired,
            sort: $sort
        );
    }

    private function createCheckboxField(
        string $formId,
        string $label,
        int $sort,
        ?string $description = null,
        bool $isRequired = true
    ): string {
        return $this->createField(
            formId: $formId,
            label: $label,
            description: $description,
            type: 'checkbox',
            isRequired: $isRequired,
            sort: $sort
        );
    }

    private function createSelectField(
        string $formId,
        string $label,
        array $options,
        int $sort,
        ?string $description = null,
        bool $isRequired = true
    ): string {
        return $this->createField(
            formId: $formId,
            label: $label,
            description: $description,
            type: 'select',
            params: ['options' => $options],
            isRequired: $isRequired,
            sort: $sort
        );
    }

    private function createTextAreaField(
        string $formId,
        string $label,
        int $sort,
        ?string $description = null,
        bool $isRequired = true
    ): string {
        return $this->createField(
            formId: $formId,
            label: $label,
            description: $description,
            type: 'textarea',
            isRequired: $isRequired,
            sort: $sort
        );
    }

    private function createPostalCodeField(
        string $formId,
        string $label,
        int $sort,
        ?string $description = null,
        bool $isRequired = true
    ): string {
        return $this->createField(
            formId: $formId,
            label: $label,
            description: $description,
            type: 'custom:postalcode',
            isRequired: $isRequired,
            sort: $sort
        );
    }

    private function createCountryField(
        string $formId,
        string $label,
        int $sort,
        ?string $description = null,
        bool $isRequired = true
    ): string {
        return $this->createField(
            formId: $formId,
            label: $label,
            description: $description,
            type: 'custom:country',
            isRequired: $isRequired,
            sort: $sort
        );
    }

    private function createBankAccountField(
        string $formId,
        string $label,
        int $sort,
        ?string $description = null,
        bool $isRequired = true
    ): string {
        return $this->createField(
            formId: $formId,
            label: $label,
            description: $description,
            type: 'custom:bankaccount',
            isRequired: $isRequired,
            sort: $sort
        );
    }

    private function createUploadField(
        string $formId,
        string $label,
        int $sort,
        ?string $description = null,
        bool $isRequired = true
    ): string {
        return $this->createField(
            formId: $formId,
            label: $label,
            description: $description,
            type: 'upload',
            isRequired: $isRequired,
            sort: $sort
        );
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sort = 0;

        $this->createSelectField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            label: 'Aanspreekvorm',
            options: ['Beste lezer', 'Beste heer', 'Beste mevrouw'],
            sort: ++$sort
        );

        $this->createTextField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            label: 'Voornaam',
            sort: ++$sort
        );

        $this->createTextField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            label: 'Tussenvoegsel',
            isRequired: false,
            sort: ++$sort
        );

        $this->createTextField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            label: 'Achternaam',
            sort: ++$sort
        );

        $this->createTextField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            label: 'Straat',
            sort: ++$sort
        );

        $this->createTextField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            label: 'Huisnummer',
            inputMode: 'numeric',
            sort: ++$sort
        );

        $this->createTextField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            label: 'Huisnummer toevoeging',
            maxLength: 10,
            isRequired: false,
            sort: ++$sort
        );

        $this->createPostalCodeField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            label: 'Postcode',
            isRequired: false,
            sort: ++$sort
        );

        $this->createTextField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            label: 'Plaats',
            maxLength: 100,
            sort: ++$sort
        );

        $this->createCountryField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            label: 'Land',
            sort: ++$sort
        );

        $this->createTextField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            label: 'Telefoonnummer',
            inputMode: 'tel',
            maxLength: 20,
            isRequired: false,
            sort: ++$sort
        );

        $this->createBankAccountField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            label: 'Rekeningnummer',
            sort: ++$sort
        );

        $this->createTextField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            label: 'Tenaamstelling rekeningnummer',
            maxLength: 50,
            sort: ++$sort
        );

        $this->createUploadField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            label: 'Kopie bankafschrift',
            sort: ++$sort
        );

        $this->createUploadField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            label: 'Uittreksel bevolkingsregister niet ouder dan 3 maanden',
            sort: ++$sort
        );

        $this->createUploadField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            label: 'Medische verklaring behandeltraject',
            sort: ++$sort
        );

        $this->createUploadField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            label: 'Medische verklaring type behandeling',
            sort: ++$sort
        );

        $this->createCheckboxField(
            formId: FormsTableSeeder::BTV_V1_UUID,
            label: 'Ik geef toestemming voor het verwerken van mijn persoonsgegevens voor deze subsidieaanvraag. Ik verklaar het formulier naar waarheid te hebben ingevuld.',
            sort: ++$sort
        );
    }
}
