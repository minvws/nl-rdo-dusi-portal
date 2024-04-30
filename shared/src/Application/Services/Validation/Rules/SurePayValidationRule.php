<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\Validation\Rules;

use Closure;
use Exception;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Support\Facades\Log;
use Illuminate\Translation\Translator;
use Illuminate\Validation\ValidationException;
use MinVWS\DUSi\Shared\Application\Repositories\BankAccount\BankAccountRepository;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\CheckOrganisationsAccountResponse;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\Enums\AccountNumberValidation;
use MinVWS\DUSi\Shared\Application\Services\Validation\SurePayValidationResultBuilder;
use MinVWS\DUSi\Shared\Application\Services\Validation\ValidationResult;
use Illuminate\Support\Collection;

class SurePayValidationRule implements
    DataAwareRule,
    ImplicitValidationRule,
    ValidationResultRule
{
    public const BANK_ACCOUNT_HOLDER_FIELD = 'bankAccountHolder';
    public const BANK_ACCOUNT_NUMBER_FIELD = 'bankAccountNumber';
    public const BANK_STATEMENT_FIELD = 'bankStatement';
    public const VALIDATION_MESSAGE_CLOSE_MATCH_SUGGESTION_PARAM = 'suggestion';

    public function __construct(
        private readonly BankAccountRepository $bankAccountRepository,
        private readonly Translator $translator,
    ) {
        $this->validationResults = collect();
    }

    private array $data = [];

    /**
     * @var Collection <ValidationResult>
     */
    private Collection $validationResults;

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @returns Collection<ValidationResult>
     */
    public function getValidationResults(): Collection
    {
        return $this->validationResults;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (
            empty($this->data[self::BANK_ACCOUNT_NUMBER_FIELD])
            || empty($this->data[self::BANK_ACCOUNT_HOLDER_FIELD])
        ) {
            return;
        }

        $validationResultBuilder = new SurePayValidationResultBuilder(
            translator: $this->translator,
            bankStatementIsSubmitted: $this->bankStatementIsSubmitted(),
        );
        try {
            $checkResult = $this->executeSurePayCheck();
        } catch (Exception $e) {
            // Catch all exceptions and log them. User needs to upload bank statement in case of exception.
            Log::error('SurePay validation failed', ['exception' => $e]);

            $this->validationResults->push($validationResultBuilder->getFailedValidationResult());
            return;
        }

        if ($checkResult->account->accountNumberValidation === AccountNumberValidation::Invalid) {
            $this->validationResults->push($validationResultBuilder->getAccountNumberInvalidValidationResult());
            return;
        }

        $validationResult = $validationResultBuilder->getValidationResult($checkResult);
        if ($validationResult !== null) {
            $this->validationResults->push($validationResult);
        }
    }

    /**
     * @throws ValidationException
     */
    private function executeSurePayCheck(): CheckOrganisationsAccountResponse
    {
        return $this->checkOrganisationsAccount(
            $this->data[self::BANK_ACCOUNT_HOLDER_FIELD] ?? '',
            $this->data[self::BANK_ACCOUNT_NUMBER_FIELD]
        );
    }

    /**
     * @throws ValidationException
     */
    private function checkOrganisationsAccount(
        string $bankAccountHolder,
        string $bankAccountNumber
    ): CheckOrganisationsAccountResponse {
        return $this->bankAccountRepository->checkOrganisationsAccount(
            $bankAccountHolder,
            $bankAccountNumber
        );
    }

    private function bankStatementIsSubmitted(): bool
    {
        return !empty($this->data[self::BANK_STATEMENT_FIELD]);
    }
}
