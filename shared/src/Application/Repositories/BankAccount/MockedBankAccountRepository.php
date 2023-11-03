<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Repositories\BankAccount;

use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\AccountInfo;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\CheckOrganisationsAccountResponse;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\Enums\AccountNumberValidation;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\Enums\NameMatchResult;

class MockedBankAccountRepository implements BankAccountRepository
{
    public const BANK_HOLDER_SUGGESTION = 'Pietersma';

    public function checkOrganisationsAccount(
        string $accountOwner,
        string $accountNumber,
        string $accountType = 'IBAN'
    ): CheckOrganisationsAccountResponse {
        [$accountNumberValidation, $nameMatchResult, $suggestion] =
            $this->bankAccountCheckMockValues($accountNumber, $accountOwner);

        return $this->createCheckOrganisationsAccountResponse($accountNumberValidation, $nameMatchResult, $suggestion);
    }

    private function createCheckOrganisationsAccountResponse(
        AccountNumberValidation $accountNumberValidation,
        NameMatchResult $nameMatchResult,
        ?string $suggestion
    ): CheckOrganisationsAccountResponse {
        return new CheckOrganisationsAccountResponse(
            account: new AccountInfo($accountNumberValidation),
            nameMatchResult: $nameMatchResult,
            nameSuggestion: $nameMatchResult === NameMatchResult::CloseMatch ? $suggestion : null
        );
    }

    private function bankAccountCheckMockValues(string $accountNumber, string $accountHolder): array
    {
        if ($this->hasAccountHolderMatchWithSuggestion($accountNumber, $accountHolder)) {
            return [
                AccountNumberValidation::Valid,
                NameMatchResult::Match,
                null
            ];
        }

        $bankAccountMockData = [
            //Valid Iban, NameMatchResult::Match
            'NL62ABNA9999841479' => [
                AccountNumberValidation::Valid,
                NameMatchResult::Match,
                null
            ],
            //NameMatchResult::NoMatch
            'NL12ABNA9999876523' => [
                AccountNumberValidation::Valid,
                NameMatchResult::NoMatch,
                null
            ],
            //NameMatchResult::CloseMatch
            'NL58ABNA9999142181' => [
                AccountNumberValidation::Valid,
                NameMatchResult::CloseMatch,
                self::BANK_HOLDER_SUGGESTION
            ],
            //NameMatchResult::NameTooShort
            'NL76ABNA9999161548' => [
                AccountNumberValidation::Valid,
                NameMatchResult::NameTooShort,
                null
            ],
            //NameMatchResult::CouldNotMatch
            'NL04RABO8731326943' => [
                AccountNumberValidation::Valid,
                NameMatchResult::CouldNotMatch,
                null
            ],
        ];

        return $bankAccountMockData[$accountNumber] ?? [AccountNumberValidation::Valid, NameMatchResult::NoMatch, null];
    }

    public function hasAccountHolderMatchWithSuggestion(string $accountNumber, string $accountHolder): bool
    {
        return $accountNumber === 'NL58ABNA9999142181' && $accountHolder === self::BANK_HOLDER_SUGGESTION;
    }
}
