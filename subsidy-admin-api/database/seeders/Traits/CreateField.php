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
                "Åland",
                "Albanië",
                "Algerije",
                "Amerikaanse Maagdeneilanden",
                "Amerikaans-Samoa",
                "Andorra",
                "Angola",
                "Anguilla",
                "Antarctica",
                "Antigua en Barbuda",
                "Argentinië",
                "Armenië",
                "Aruba",
                "Australië",
                "Azerbeidzjan",
                "Bahama’s",
                "Bahrein",
                "Bangladesh",
                "Barbados",
                "België",
                "Belize",
                "Benin",
                "Bermuda",
                "Bhutan",
                "Bolivia",
                "Bosnië en Herzegovina",
                "Botswana",
                "Bouveteiland",
                "Brazilië",
                "Britse Maagdeneilanden",
                "Brits Indische Oceaanterritorium",
                "Brunei",
                "Bulgarije",
                "Burkina Faso",
                "Burundi",
                "Cambodja",
                "Canada",
                "Centraal-Afrikaanse Republiek",
                "Chili",
                "China",
                "Christmaseiland",
                "Cocoseilanden",
                "Colombia",
                "Comoren",
                "Congo-Brazzaville",
                "Congo-Kinshasa",
                "Cookeilanden",
                "Costa Rica",
                "Cuba",
                "Curaçao",
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
                "Faeröer",
                "Falklandeilanden",
                "Fiji",
                "Filipijnen",
                "Finland",
                "Frankrijk",
                "Franse Zuidelijke en Antarctische Gebieden",
                "Frans-Guyana",
                "Frans-Polynesië",
                "Gabon",
                "Gambia",
                "Georgië",
                "Ghana",
                "Gibraltar",
                "Grenada",
                "Griekenland",
                "Groenland",
                "Guadeloupe",
                "Guam",
                "Guatemala",
                "Guernsey",
                "Guinee",
                "Guinee-Bissau",
                "Guyana",
                "Haïti",
                "Heard en McDonaldeilanden",
                "Honduras",
                "Hongarije",
                "Hongkong",
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
                "Jersey",
                "Jordanië",
                "Kaaimaneilanden",
                "Kaapverdië",
                "Kameroen",
                "Kazachstan",
                "Kenia",
                "Kirgizië",
                "Kiribati",
                "Kleine Pacifische eilanden van de Verenigde Staten",
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
                "Macau",
                "Madagaskar",
                "Malawi",
                "Maldiven",
                "Maleisië",
                "Mali",
                "Malta",
                "Marokko",
                "Marshalleilanden",
                "Martinique",
                "Mauritanië",
                "Mauritius",
                "Mayotte",
                "Mexico",
                "Micronesia",
                "Moldavië",
                "Monaco",
                "Mongolië",
                "Montenegro",
                "Montserrat",
                "Mozambique",
                "Myanmar",
                "Namibië",
                "Nauru",
                "Nederland",
                "Nepal",
                "Nicaragua",
                "Nieuw-Caledonië",
                "Nieuw-Zeeland",
                "Niger",
                "Nigeria",
                "Niue",
                "Noordelijke Marianen",
                "Noord-Korea",
                "Noord-Macedonië",
                "code Land",
                "Noorwegen",
                "Norfolk",
                "Oeganda",
                "Oekraïne",
                "Oezbekistan",
                "Oman",
                "Oostenrijk",
                "Oost-Timor",
                "Pakistan",
                "Palau",
                "Palestina",
                "Panama",
                "Papoea-Nieuw-Guinea",
                "Paraguay",
                "Peru",
                "Pitcairneilanden",
                "Polen",
                "Portugal",
                "Puerto Rico",
                "Qatar",
                "Réunion",
                "Roemenië",
                "Rusland",
                "Rwanda",
                "Saint-Barthélemy",
                "Saint Kitts en Nevis",
                "Saint Lucia",
                "Saint-Pierre en Miquelon",
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
                "Sint-Helena, Ascension en Tristan da Cunha",
                "Sint-Maarten",
                "Sint Maarten",
                "Slovenië",
                "Slowakije",
                "Soedan",
                "Somalië",
                "Spanje",
                "Spitsbergen en Jan Mayen",
                "Sri Lanka",
                "Suriname",
                "Swaziland",
                "Syrië",
                "Tadzjikistan",
                "Taiwan",
                "Tanzania",
                "Thailand",
                "Togo",
                "Tokelau",
                "Tonga",
                "Trinidad en Tobago",
                "Tsjaad",
                "Tsjechië",
                "Tunesië",
                "Turkije",
                "Turkmenistan",
                "Turks- en Caicoseilanden",
                "Tuvalu",
                "Uruguay",
                "Vanuatu",
                "Vaticaanstad",
                "Venezuela",
                "Verenigde Arabische Emiraten",
                "Verenigde Staten",
                "Verenigd Koninkrijk",
                "Vietnam",
                "Wallis en Futuna",
                "Westelijke Sahara",
                "Wit-Rusland",
                "Zambia",
                "Zimbabwe",
                "Zuid-Afrika",
                "Zuid-Georgia en de Zuidelijke Sandwicheilanden",
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
        bool $isRequired = true,
        ?array $mimeTypes = null,
        ?int $maxFileSize = null,
    ): string {
        return $this->createField(
            subsidyStageId: $subsidyStageId,
            code: $code,
            title: $title,
            description: $description,
            type: 'upload',
            isRequired: $isRequired,
            params: array_filter([
                'mimeTypes' => $mimeTypes,
                'maxFileSize' => $maxFileSize,
            ]),
        );
    }
}
