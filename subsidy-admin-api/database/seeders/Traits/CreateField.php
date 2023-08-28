<?php
/** @noinspection PhpReturnValueOfMethodIsNeverUsedInspection, PhpUnusedPrivateMethodInspection, SpellCheckingInspection, PhpSameParameterValueInspection, PhpNamedArgumentsWithChangedOrderInspection */

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\Traits;

use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

trait CreateField
{
    private function createField(
        string $subsidyStageId,
        string $code,
        string $title,
        string $type,
        ?string $description = null,
        ?array $params = null,
        bool $isRequired = true
    ): string {
        $id = Uuid::uuid4()->toString();

        DB::table('fields')->insert([
            'id' => $id,
            'subsidy_stage_id' => $subsidyStageId,
            'code' => $code,
            'title' => $title,
            'description' => $description,
            'type' => $type,
            'params' => json_encode($params),
            'is_required' => $isRequired,
        ]);

        return $id;
    }

    private function createTextField(
        string $subsidyStageId,
        string $code,
        string $title,
        ?string $description = null,
        ?string $inputMode = null,
        ?int $maxLength = null,
        bool $isRequired = true
    ): string {
        return $this->createField(
            subsidyStageId: $subsidyStageId,
            code: $code,
            title: $title,
            description: $description,
            type: $inputMode !== null ? "text:$inputMode" : 'text',
            params: ['maxLength' => $maxLength],
            isRequired: $isRequired,
        );
    }

    private function createDateField(
        string $subsidyStageId,
        string $code,
        string $title,
        ?string $description = null,
        bool $isRequired = true
    ): string {
        return $this->createField(
            subsidyStageId: $subsidyStageId,
            code: $code,
            title: $title,
            description: $description,
            type: 'date',
            isRequired: $isRequired
        );
    }

    private function createMultiSelectField(
        string $subsidyStageId,
        string $code,
        string $title,
        array $options,
        ?string $description = null,
        bool $isRequired = true
    ): string {
        return $this->createField(
            subsidyStageId: $subsidyStageId,
            code: $code,
            title: $title,
            description: $description,
            type: 'multiselect',
            params: ['options' => $options],
            isRequired: $isRequired,
        );
    }

    private function createCheckboxField(
        string $subsidyStageId,
        string $code,
        string $title,
        ?string $description = null,
        bool $isRequired = true
    ): string {
        return $this->createField(
            subsidyStageId: $subsidyStageId,
            code: $code,
            title: $title,
            description: $description,
            type: 'checkbox',
            isRequired: $isRequired,
        );
    }

    private function createSelectField(
        string $subsidyStageId,
        string $code,
        string $title,
        array $options,
        ?string $description = null,
        bool $isRequired = true
    ): string {
        return $this->createField(
            subsidyStageId: $subsidyStageId,
            code: $code,
            title: $title,
            description: $description,
            type: 'select',
            params: ['options' => $options],
            isRequired: $isRequired,
        );
    }

    private function createTextAreaField(
        string $subsidyStageId,
        string $code,
        string $title,
        ?string $description = null,
        bool $isRequired = true
    ): string {
        return $this->createField(
            subsidyStageId: $subsidyStageId,
            code: $code,
            title: $title,
            description: $description,
            type: 'textarea',
            isRequired: $isRequired,
        );
    }

    private function createPostalCodeField(
        string $subsidyStageId,
        string $code,
        string $title,
        ?string $description = null,
        bool $isRequired = true
    ): string {
        return $this->createField(
            subsidyStageId: $subsidyStageId,
            code: $code,
            title: $title,
            description: $description,
            type: 'custom:postalcode',
            isRequired: $isRequired,
        );
    }

    private function createCountryField(
        string $subsidyStageId,
        string $code,
        string $title,
        ?string $description = null,
        bool $isRequired = true
    ): string {
        return $this->createSelectField(
            subsidyStageId: $subsidyStageId,
            code: $code,
            title: $title,
            description: $description,
            options: [
                "Afghanistan",
                "Albanië",
                "Algerije",
                "Andorra",
                "Angola",
                "Antigua en Barbuda",
                "Argentinië",
                "Armenië",
                "Australië",
                "Azerbeidzjan",
                "Bahama`s",
                "Bahrein",
                "Bangladesh",
                "Barbados",
                "België",
                "Belize",
                "Benin",
                "Bhutan",
                "Bolivia",
                "Bosnië en Herzegovina",
                "Botswana",
                "Brazilië",
                "Brunei",
                "Bulgarije",
                "Burkina Faso",
                "Burundi",
                "Cambodja",
                "Canada",
                "Centraal-Afrikaanse Republiek",
                "Chili",
                "China",
                "Colombia",
                "Comoren",
                "Congo-Brazzaville",
                "Congo-Kinshasa",
                "Costa Rica",
                "Cuba",
                "Cyprus",
                "Denemarken",
                "Djibouti",
                "Dominica",
                "Dominicaanse Republiek",
                "Duitsland",
                "Ecuador",
                "Egypte",
                "El Salvador",
                "Equatoriaal-Guinea",
                "Eritrea",
                "Estland",
                "Ethiopië",
                "Fiji",
                "Filipijnen",
                "Finland",
                "Frankrijk",
                "Gabon",
                "Gambia",
                "Georgië",
                "Ghana",
                "Grenada",
                "Griekenland",
                "Guatemala",
                "Guinee",
                "Guinee-Bissau",
                "Guyana",
                "Haïti",
                "Honduras",
                "Hongarije",
                "Ierland",
                "IJsland",
                "India",
                "Indonesië",
                "Irak",
                "Iran",
                "Israël",
                "Italië",
                "Ivoorkust",
                "Jamaica",
                "Japan",
                "Jemen",
                "Jordanië",
                "Kaapverdië",
                "Kameroen",
                "Kazachstan",
                "Kenia",
                "Kirgizië",
                "Kiribati",
                "Koeweit",
                "Kroatië",
                "Laos",
                "Lesotho",
                "Letland",
                "Libanon",
                "Liberia",
                "Libië",
                "Liechtenstein",
                "Litouwen",
                "Luxemburg",
                "Madagaskar",
                "Malawi",
                "Malediven",
                "Maleisië",
                "Mali",
                "Malta",
                "Marokko",
                "Marshalleilanden",
                "Mauritanië",
                "Mauritius",
                "Mexico",
                "Micronesië",
                "Moldavië",
                "Monaco",
                "Mongolië",
                "Montenegro",
                "Mozambique",
                "Myanmar",
                "Namibië",
                "Nauru",
                "Nederland",
                "Nepal",
                "Nicaragua",
                "Nieuw-Zeeland",
                "Niger",
                "Nigeria",
                "Noord-Korea",
                "Noord-Macedonië",
                "Noorwegen",
                "Oeganda",
                "Oekraïne",
                "Oezbekistan",
                "Oman",
                "Oostenrijk",
                "Oost-Timor",
                "Pakistan",
                "Palau",
                "Panama",
                "Papoea-Nieuw-Guinea",
                "Paraguay",
                "Peru",
                "Polen",
                "Portugal",
                "Qatar",
                "Roemenië",
                "Rusland",
                "Rwanda",
                "Saint Kitts en Nevis",
                "Saint Lucia",
                "Saint Vincent en de Grenadines",
                "Salomonseilanden",
                "Samoa",
                "San Marino",
                "Saoedi-Arabië",
                "Sao Tomé en Principe",
                "Senegal",
                "Servië",
                "Seychellen",
                "Sierra Leone",
                "Singapore",
                "Slovenië",
                "Slowakije",
                "Soedan",
                "Somalië",
                "Spanje",
                "Sri Lanka",
                "Suriname",
                "Swaziland",
                "Syrië",
                "Tadzjikistan",
                "Tanzania",
                "Thailand",
                "Togo",
                "Tonga",
                "Trinidad en Tobago",
                "Tsjaad",
                "Tsjechië",
                "Tunesië",
                "Turkije",
                "Turkmenistan",
                "Tuvalu",
                "Uruguay",
                "Vanuatu",
                "Venezuela",
                "Verenigde Arabische Emiraten",
                "Verenigde Staten",
                "Verenigd Koninkrijk",
                "Vietnam",
                "Wit-Rusland",
                "Zambia",
                "Zimbabwe",
                "Zuid-Afrika",
                "Zuid-Korea",
                "Zuid-Soedan",
                "Zweden",
                "Zwitserland"
            ],
            isRequired: $isRequired,
        );
    }

    private function createBankAccountField(
        string $subsidyStageId,
        string $code,
        string $title,
        ?string $description = null,
        bool $isRequired = true
    ): string {
        return $this->createField(
            subsidyStageId: $subsidyStageId,
            code: $code,
            title: $title,
            description: $description,
            type: 'custom:bankaccount',
            isRequired: $isRequired,
        );
    }

    private function createUploadField(
        string $subsidyStageId,
        string $code,
        string $title,
        ?string $description = null,
        bool $isRequired = true
    ): string {
        return $this->createField(
            subsidyStageId: $subsidyStageId,
            code: $code,
            title: $title,
            description: $description,
            type: 'upload',
            isRequired: $isRequired,
        );
    }
}
