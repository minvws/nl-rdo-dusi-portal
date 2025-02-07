<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\Validation;

use Illuminate\Support\Str;
use Illuminate\Translation\Translator;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\CheckOrganisationsAccountResponse;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\Enums\NameMatchResult;
use MinVWS\DUSi\Shared\Application\Services\Validation\Rules\SurePayValidationRule;

readonly class SurePayValidationResultBuilder
{
    public function __construct(
        protected Translator $translator,
        protected bool $bankStatementIsSubmitted = false,
    ) {
    }

    public function getValidationResult(
        CheckOrganisationsAccountResponse $response
    ): ?ValidationResult {
        $nameMatchResult = $response->nameMatchResult;

        $params = match ($nameMatchResult) {
            NameMatchResult::CloseMatch => $this->getCloseMatchParameters($response),
            default => [],
        };

        return match ($nameMatchResult) {
            NameMatchResult::Match => $this->createValidValidationResult(),
            NameMatchResult::CloseMatch,
            NameMatchResult::NoMatch,
            NameMatchResult::CouldNotMatch,
            NameMatchResult::NameTooShort => $this->getValidationResultForNameResult($nameMatchResult, $params),
        };
    }

    public function getFailedValidationResult(): ValidationResult
    {
        return $this->createInvalidValidationResult(
            message: $this->translator->get('validateFields.surepay_validation_failed'),
            bankStatementIsSubmitted: $this->bankStatementIsSubmitted,
        );
    }

    public function getAccountNumberInvalidValidationResult(): ValidationResult
    {
        return $this->createInvalidValidationResult(
            message: $this->translator->get('validateFields.surepay_validation_account_number_invalid'),
            bankStatementIsSubmitted: $this->bankStatementIsSubmitted,
        );
    }

    /**
     * @param CheckOrganisationsAccountResponse $organisationsAccountResponse
     * @return array<string, ValidationResultParam>
     */
    protected function getCloseMatchParameters(
        CheckOrganisationsAccountResponse $organisationsAccountResponse
    ): array {
        $nameSuggestion = $organisationsAccountResponse->nameSuggestion;

        $params = [];
        if (!empty($nameSuggestion)) {
            $params[SurePayValidationRule::VALIDATION_MESSAGE_CLOSE_MATCH_SUGGESTION_PARAM] = new ValidationResultParam(
                code: SurePayValidationRule::BANK_ACCOUNT_HOLDER_FIELD,
                value: $nameSuggestion,
            );
        }

        return $params;
    }

    /**
     * @param NameMatchResult $nameMatchResult
     * @param array<string, ValidationResultParam> $params
     * @return ValidationResult
     */
    protected function getValidationResultForNameResult(
        NameMatchResult $nameMatchResult,
        array $params = []
    ): ValidationResult {
        $message = $this->getTranslatedMessageFromNameMatchResult($nameMatchResult);

        return $this->createInvalidValidationResult(
            message: $message,
            params: $params,
            bankStatementIsSubmitted: $this->bankStatementIsSubmitted,
        );
    }

    protected function createValidValidationResult(): ValidationResult
    {
        return ValidationResultFactory::createConfirmation(
            message: $this->getTranslatedMessageFromNameMatchResult(NameMatchResult::Match),
            id: 'validationSurePayValid'
        );
    }

    /**
     * @param string $message
     * @param array<string, ValidationResultParam> $params
     * @param bool $bankStatementIsSubmitted
     * @return ValidationResult
     */
    protected function createInvalidValidationResult(
        string $message,
        array $params = [],
        bool $bankStatementIsSubmitted = false
    ): ValidationResult {
        $validationResult = ValidationResultFactory::createError(
            message: $message,
            id: 'validationSurePayError'
        );
        if ($bankStatementIsSubmitted) {
            $validationResult = ValidationResultFactory::createWarning(
                message: $message,
                id: 'validationSurePayWarning'
            );
        }
        foreach ($params as $key => $value) {
            $validationResult->setParam($key, $value);
        }
        return $validationResult;
    }

    protected function getTranslatedMessageFromNameMatchResult(
        NameMatchResult $nameMatchResult
    ): string {
        $lowerNameMatchResult = Str::lower($nameMatchResult->value);

        return (string) $this->translator->get(
            sprintf('validateFields.surepay_validation_name_%s', $lowerNameMatchResult)
        );
    }
}
