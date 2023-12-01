<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationSurePayResult;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\Enums\AccountNumberValidation;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\Enums\AccountStatus;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\Enums\AccountType;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\Enums\NameMatchResult;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\Enums\PaymentPreValidation;

/**
 * @extends Factory<ApplicationSurePayResult>
 */
class ApplicationSurePayResultFactory extends Factory
{
    protected $model = ApplicationSurePayResult::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'application_id' => fn () => Application::factory(),
            'name_match_result' => NameMatchResult::Match,
            'account_number_validation' => AccountNumberValidation::Valid,
            'payment_pre_validation' => PaymentPreValidation::Pass,
            'status' => AccountStatus::Active,
            'account_type' => AccountType::NaturalPerson,
            'joint_account' => false,
            'number_of_account_holders' => rand(1, 5),
            'country_code' => 'NL',
            'created_at' => $this->faker->dateTimeBetween('-1 month'),
        ];
    }
}
