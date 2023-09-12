<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Tests\Unit\Services\SurePay\Fakes;

use Carbon\Carbon;
use Faker\Factory;
use MinVWS\DUSi\Application\Backend\Services\SurePay\DTO\AccesstokenResponse;


class AccesstokenResponseFake extends AccesstokenResponse
{
    public function __construct()
    {
        $faker = Factory::create();

        $response = [
            'refresh_token_expires_in' => $faker->numberBetween(0, 10),
            'api_product_list' => implode(',', $faker->words(4)),
            'api_product_list_json' => $faker->words(4),
            'organization_name' => $faker->company(),
            'developer.email' => $faker->companyEmail(),
            'token_type' => $faker->randomElement(['Bearer', 'OAuth']),
            'issued_at' => Carbon::now()->getTimestamp(),
            'client_id' => $faker->uuid(),
            'access_token' => $faker->md5(),
            'application_name' => implode(' ', [$faker->company, $faker->slug]),
            'scope' => implode(',', $faker->words(1)),
            'expires_in' => $faker->numberBetween(1000, 5000),
            'refresh_count' => 0,
            'status' => 'approved',
        ];

        parent::__construct($response);
    }
}
