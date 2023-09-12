<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Tests\Unit\Services\SurePay\Fakes;

use Faker\Factory;
use MinVWS\DUSi\Application\Backend\Services\SurePay\DTO\CheckOrganisationsAccountResponse;

class CheckOrganisationsAccountResponseFake extends CheckOrganisationsAccountResponse{
    public function __construct()
    {
        $faker = Factory::create();

        $response = [
            'accountNumberValidation' => 'VALID',
            'paymentPreValidation' => 'PASS',
            'status' => 'ACTIVE',
            'accountType' => 'NP',
            'jointAccount' => $faker->boolean,
            'numberOfAccountHolders' => $faker->numberBetween(1, 5),
            'countryCode' => $faker->countryCode
        ];

        parent::__construct($response);
    }
}
