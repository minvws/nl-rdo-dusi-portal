<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO;

use MinVWS\Codable\Decoding\Decodable;
use MinVWS\Codable\Decoding\DecodableSupport;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\Enums\AccountNumberValidation;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\Enums\AccountStatus;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\Enums\AccountType;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\Enums\PaymentPreValidation;

readonly class AccountInfo implements Decodable
{
    use DecodableSupport;

    public function __construct(
        public AccountNumberValidation $accountNumberValidation,
        public ?PaymentPreValidation $paymentPreValidation = null,
        public ?AccountStatus $status = null,
        public ?AccountType $accountType = null,
        public ?bool $jointAccount = null,
        public ?int $numberOfAccountHolders = null,
        public ?string $countryCode = null
    ) {
    }
}
