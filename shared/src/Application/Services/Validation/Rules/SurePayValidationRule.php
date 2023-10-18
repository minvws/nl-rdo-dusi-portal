<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\Validation\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use MinVWS\DUSi\Shared\Application\Models\ApplicationSurePayResult;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\Enums\NameMatchResult;
use MinVWS\DUSi\Shared\Application\Services\SurePayService;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\Condition;

class SurePayValidationRule implements DataAwareRule, ImplicitValidationRule, SuccessMessageResultRule
{
    public function __construct(
        private readonly SurePayService $surePayService
    ) {
    }

    private array $data = [];

    private array $successMessages = [];

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function getSuccessMessages(): array
    {
        return $this->successMessages;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if(empty($this->data['bankAccountHolder']) || empty($this->data['bankAccountNumber'])) {
            //todo split and communicate to frontend
            $fail('validation.required', ['attribute' => 'bankAccountHolder']);
        }
        $result = $this->surePayService->checkOrganisationsAccount(
            $this->data['bankAccountHolder'],
            $this->data['bankAccountNumber']
        );
        if($result->nameMatchResult === NameMatchResult::NoMatch) {
            //todo split and communicate to frontend
            $fail('icon-failed');
        } else {
            array_push($this->successMessages, 'icon-success');
        }
    }
}
