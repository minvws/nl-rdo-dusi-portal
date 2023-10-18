<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\Validation\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Validation\ValidationException;
use MinVWS\DUSi\Shared\Application\Models\ApplicationSurePayResult;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\CheckOrganisationsAccountResponse;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\Enums\NameMatchResult;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\SurePayClient;
use MinVWS\DUSi\Shared\Application\Services\SurePayService;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\Condition;
use RuntimeException;

class SurePayValidationRule implements DataAwareRule, ImplicitValidationRule, SuccessMessageResultRule
{
    public function __construct(
        private readonly ?SurePayClient $surePayClient
    ) {
    }

    private array $data = [];

    private array $successMessages = [];

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @returns array<string>
     */
    public function getSuccessMessages(): array
    {
        return $this->successMessages;
    }

    /**
     * @throws ValidationException
     */
    public function checkOrganisationsAccount(
        string $bankAccountHolder,
        string $bankAccountNumber
    ): CheckOrganisationsAccountResponse {
        if (! isset($this->surePayClient)) {
            throw new RuntimeException('surePayClient is not set');
        }
        return $this->surePayClient->checkOrganisationsAccount(
            $bankAccountHolder,
            $bankAccountNumber
        );
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($this->data['bankAccountNumber'])) {
            $fail('validation.required');
        }
        $result = $this->checkOrganisationsAccount(
            $this->data['bankAccountHolder'] ?? '',
            $this->data['bankAccountNumber']
        );
        if ($result->nameMatchResult === NameMatchResult::NoMatch) {
            $fail('icon-failed');
        } else {
            array_push($this->successMessages, 'icon-success');
        }
    }
}
