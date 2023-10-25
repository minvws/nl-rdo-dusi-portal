<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\Validation\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Support\Str;
use Illuminate\Translation\Translator;
use Illuminate\Validation\ValidationException;
use MinVWS\DUSi\Shared\Application\Models\ApplicationSurePayResult;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\CheckOrganisationsAccountResponse;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\Enums\NameMatchResult;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\SurePayClient;
use MinVWS\DUSi\Shared\Application\Services\SurePayService;
use MinVWS\DUSi\Shared\Subsidy\Models\Condition\Condition;
use RuntimeException;

class SurePayValidationRule implements
    DataAwareRule,
    ImplicitValidationRule,
    SuccessMessageResultRule,
    ErrorMessageResultRule
{
    public function __construct(
        private readonly ?SurePayClient $surePayClient,
        private readonly Translator $translator,
    ) {
    }

    private array $data = [];

    private array $successMessages = [];

    private array $errorMessages = [];

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
     * @returns array<string>
     */
    public function getErrorMessages(): array
    {
        return $this->errorMessages;
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
        $this->validateBankAccountData($fail);
        $checkResult = $this->checkOrganisationAccountData();

        $lowerNameMatchResult = Str::lower($checkResult->nameMatchResult->value);
        $validationResponse = $this->prepareValidationResponse($lowerNameMatchResult);

        $this->processMatchResults($checkResult, $validationResponse, $fail);
    }

    protected function validateBankAccountData(Closure $fail): void
    {
        if (empty($this->data['bankAccountNumber'])) {
            $fail('validation.required');
        }
    }

    /**
     * @throws ValidationException
     */
    protected function checkOrganisationAccountData(): CheckOrganisationsAccountResponse
    {
        return $this->checkOrganisationsAccount(
            $this->data['bankAccountHolder'] ?? '',
            $this->data['bankAccountNumber']
        );
    }

    protected function prepareValidationResponse(string $result): array
    {
        return [
            'message' => $this->getTranslation($result),
            'icon' => sprintf('icon_%s', $result),
        ];
    }

    protected function processMatchResults(
        CheckOrganisationsAccountResponse $checkResult,
        array &$validationResponse,
        Closure $fail
    ): void {
        if ($checkResult->nameMatchResult === NameMatchResult::NoMatch) {
            array_push($this->errorMessages, $validationResponse);
            $fail($validationResponse['message']);
        } elseif ($checkResult->nameMatchResult === NameMatchResult::CloseMatch) {
            $validationResponse['suggestion'] = $checkResult->nameSuggestion;
            array_push($this->successMessages, $validationResponse);
        } elseif ($checkResult->nameMatchResult === NameMatchResult::Match) {
            array_push($this->successMessages, $validationResponse);
        }
        //Other results need no response in Application portal
    }

    public function getTranslation(string $lowerNameMatchResult): string|array|null
    {
        return $this->translator->get(
            sprintf('validateFields.validation_surepay_%s', $lowerNameMatchResult),
            locale: 'nl'
        );
    }
}
