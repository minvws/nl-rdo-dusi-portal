<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Tests\Unit\SurePay\Fakes;

use Faker\Factory;

class CheckOrganisationsAccountResponseFake
{
    public static function build(): array
    {
        $faker = Factory::create();

        return [
            'nameMatchResult' => 'MATCH',
            'account' => [
                'accountNumberValidation' => 'VALID',
                'paymentPreValidation' => 'PASS',
                'status' => 'ACTIVE',
                'accountType' => 'NP',
                'jointAccount' => $faker->boolean,
                'numberOfAccountHolders' => $faker->numberBetween(1, 5),
                'countryCode' => $faker->countryCode
            ]
        ];
    }
}
