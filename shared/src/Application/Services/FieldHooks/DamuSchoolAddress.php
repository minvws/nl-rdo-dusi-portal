<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\FieldHooks;

use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Submission\FieldValue;

class DamuSchoolAddress implements FieldHook
{
    private const SUBSIDY_DAMU_UUID = '7b9f1318-4c38-4fe5-881b-074729d95abf';

    public function isHookActive(ApplicationStage $applicationStage): bool
    {
        return $applicationStage->subsidyStage->subsidyVersion->subsidy_id === self::SUBSIDY_DAMU_UUID;
    }

    public function run(FieldValue $fieldValue, array $fieldValues, ApplicationStage $applicationStage): FieldValue
    {
        if (
            $fieldValues['educationType']->value === null ||
            ($fieldValues['damuSchoolPrimary']->value === null &&
            $fieldValues['damuSchoolSecondary']->value === null)
        ) {
            return  new FieldValue($fieldValue->field, null);
        }

        if (
            $fieldValues['educationType']->value === EducationalType::PRIMARY_EDUCATION &&
            $fieldValues['damuSchoolPrimary']->value !== null
        ) {
            return new FieldValue(
                $fieldValue->field,
                $this->getPrimarySchoolAddress($fieldValues['damuSchoolPrimary']->value)
            );
        }

        if (
            $fieldValues['educationType']->value === EducationalType::SECONDARY_EDUCATION &&
            $fieldValues['damuSchoolSecondary']->value !== null
        ) {
            return new FieldValue(
                $fieldValue->field,
                $this->getSecondarySchoolAddress($fieldValues['damuSchoolSecondary']->value)
            );
        }

        return  new FieldValue($fieldValue->field, null);
    }

    private function getPrimarySchoolAddress(string $damuSchoolPrimary): string
    {
        return match ($damuSchoolPrimary) {
            "Amsterdam - Olympiaschool" => "Stadionkade 113, 1076 BN, Amsterdam",
            "Den Haag - School voor Jong Talent" => "Turfmarkt 7, 2511 DK, Den Haag",
            "Rotterdam - Nieuwe Park Rozenburgschool" => "Hoflaan 113, 3062 JE, Rotterdam",
            default => "Adres niet gevonden",
        };
    }

    private function getSecondarySchoolAddress(string $damuSchoolPrimary): string
    {
        return match ($damuSchoolPrimary) {
            "Amsterdam - Gerrit van der Veen College" => "Gerrit van der Veenstraat 99, 1077 DT, Amsterdam",
            "Amsterdam - Individueel Voortgezet Kunstzinnig Onderwijs (IVKO)" =>
                "Rustenburgerstraat 15, 1074 EP, Amsterdam",
            "Arnhem - Beekdal Lyceum" => "Bernhardlaan 49, 6824 LE, Arnhem",
            "Den Haag - Interfaculteit School voor Jong Talent" => "Turfmarkt 7, 2511 DK, Den Haag",
            "Enschede - Het Stedelijk Lyceum, locatie Kottenpark" => "Lyceumlaan 30, 7522 GK, Enschede",
            "Haren - Zernike College" => "Kerklaan 39, 9751 NL, Haren",
            "Maastricht - Bonnefanten College" => "Tongerseweg 135, 6213 GB, Maastricht",
            "Rotterdam - Havo/Vwo voor muziek en dans" => "Kruisplein 26, 3012 CC, Rotterdam",
            "Rotterdam - Thorbecke Voortgezet Onderwijs" => "Prinsenlaan 82, 3066 KA, Rotterdam",
            "Tilburg - Koning Willem II College" => "Tatraweg 80, 5022 DS, Tilburg",
            "Venlo - Valuas College" => "Hogeweg 24, 5911 EB, Venlo",
            default => "Adres niet gevonden",
        };
    }
}
