<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Repositories\BankAccount;

use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\CheckOrganisationsAccountResponse;

interface BankAccountRepository
{
    public function checkOrganisationsAccount(
        string $accountOwner,
        string $accountNumber,
        string $accountType = 'IBAN'
    ): CheckOrganisationsAccountResponse;
}
