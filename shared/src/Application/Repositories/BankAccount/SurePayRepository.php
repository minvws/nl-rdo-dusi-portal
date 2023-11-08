<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Repositories\BankAccount;

use Illuminate\Validation\ValidationException;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\CheckOrganisationsAccountResponse;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\SurePayClient;
use RuntimeException;

class SurePayRepository implements BankAccountRepository
{
    public function __construct(
        protected SurePayClient $surePayClient,
    ) {
    }

    /**
     * @throws ValidationException
     */
    public function checkOrganisationsAccount(
        string $accountOwner,
        string $accountNumber,
        string $accountType = 'IBAN'
    ): CheckOrganisationsAccountResponse {

        if (! isset($this->surePayClient)) {
            throw new RuntimeException('surePayClient is not set');
        }

        return $this->surePayClient->checkOrganisationsAccount($accountOwner, $accountNumber, $accountType);
    }
}
