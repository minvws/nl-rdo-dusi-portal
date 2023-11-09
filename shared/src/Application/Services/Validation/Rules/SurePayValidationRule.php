<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\Validation\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Translation\Translator;
use Illuminate\Validation\ValidationException;
use MinVWS\DUSi\Shared\Application\Repositories\BankAccount\BankAccountRepository;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\CheckOrganisationsAccountResponse;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\Enums\AccountNumberValidation;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\Enums\NameMatchResult;
use MinVWS\DUSi\Shared\Application\Services\Validation\ValidationResult;
use MinVWS\DUSi\Shared\Application\Services\Validation\ValidationResultFactory;
use Illuminate\Support\Collection;
use MinVWS\DUSi\Shared\Application\Services\Validation\ValidationResultParam;

class SurePayValidationRule implements
    DataAwareRule,
    ImplicitValidationRule,
    ValidationResultRule
{
    private const BANK_ACCOUNT_HOLDER = 'bankAccountHolder';
    private const BANK_ACCOUNT_NUMBER = 'bankAccountNumber';
    private const CLOSE_MATCH_SUGGESTION = 'suggestion';

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
        if (empty($this->data[self::BANK_ACCOUNT_NUMBER]) || empty($this->data[self::BANK_ACCOUNT_HOLDER])) {
            return;
        }

        $checkResult = $this->executeSurePayCheck();

        $this->validateBankAccountNumber($checkResult, $fail);
        $this->processNameMatchResults($checkResult, $fail);
    }

    private function validateBankAccountNumber(CheckOrganisationsAccountResponse $checkResult, Closure $fail): void
    {
        if ($checkResult->account->accountNumberValidation === AccountNumberValidation::Invalid) {
            $fail($this->translator->get('validateFields.iban_not_valid'));
        }
    }

    /**
     * @throws ValidationException
     */
    private function executeSurePayCheck(): CheckOrganisationsAccountResponse
    {
        return $this->checkOrganisationsAccount(
            $this->data[self::BANK_ACCOUNT_HOLDER] ?? '',
            $this->data[self::BANK_ACCOUNT_NUMBER]
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

    protected function processNameMatchResults(CheckOrganisationsAccountResponse $checkResult, Closure $fail): void
    {
        $message = $this->getTranslatedMessageFromNameMatchResult($checkResult);
        $nameMatchResult = $checkResult->nameMatchResult;

        if ($nameMatchResult === NameMatchResult::NoMatch) {
            $this->handleNoMatch($message, $fail);
        }

        if ($nameMatchResult === NameMatchResult::CloseMatch) {
            $this->handleCloseMatch($checkResult, $message);
        }

        if ($nameMatchResult === NameMatchResult::Match) {
            $this->handleMatch($message);
        }

        //Other results need no response in Application portal
    }

    private function handleNoMatch(string $message, Closure $fail): void
    {
        $this->validationResults->push(ValidationResultFactory::createError(message: $message));
        $fail($message);
    }

    private function handleCloseMatch(CheckOrganisationsAccountResponse $checkResult, string $message): void
    {
        $validationResult = ValidationResultFactory::createWarning(message: $message);

        if ($checkResult->nameSuggestion) {
            $validationResult->setParam(
                self::CLOSE_MATCH_SUGGESTION,
                new ValidationResultParam(self::BANK_ACCOUNT_HOLDER, $checkResult->nameSuggestion)
            );
        }

        $this->validationResults->push($validationResult);
    }

    private function handleMatch(string $message): void
    {
        $this->validationResults->push(ValidationResultFactory::createConfirmation(message: $message));
    }

    private function getTranslatedMessageFromNameMatchResult(
        CheckOrganisationsAccountResponse $checkResult
    ): string {
        $lowerNameMatchResult = Str::lower($checkResult->nameMatchResult->value);
        $message = $this->getSurePayValidationTranslation($lowerNameMatchResult);

        return $message;
    }

    public function getSurePayValidationTranslation(string $lowerNameMatchResult): string
    {
        return (string) $this->translator->get(
            sprintf('validateFields.validation_surepay_%s', $lowerNameMatchResult)
        );
    }
}
