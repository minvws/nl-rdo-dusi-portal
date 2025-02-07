<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Repositories\BankAccount;

use Illuminate\Contracts\Redis\Connection;
use MinVWS\DUSi\Shared\Application\DTO\SurepayServiceHealth;
use Exception;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\CheckOrganisationsAccountResponse;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\Exceptions\SurePayMaxRetryException;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\Exceptions\SurePayRepositoryException;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\SurePayClient;
use RuntimeException;

class SurePayRepository implements BankAccountRepository
{
    public function __construct(
        protected SurePayClient $surePayClient,
        protected Connection $redisConnection,
    ) {
    }

    /**
     * @throws SurePayRepositoryException
     * @throws SurePayMaxRetryException
     * @throws Exception
     */
    public function checkOrganisationsAccount(
        string $accountOwner,
        string $accountNumber,
        string $accountType = 'IBAN'
    ): CheckOrganisationsAccountResponse {

        if (! isset($this->surePayClient)) {
            throw new RuntimeException('surePayClient is not set');
        }

        try {
            return $this->surePayClient->checkOrganisationsAccount($accountOwner, $accountNumber, $accountType);
        } catch (SurePayMaxRetryException $e) {
            SurepayServiceHealth::increaseSurePayFailedCounter($this->redisConnection);

            throw $e;
        }
    }
}
