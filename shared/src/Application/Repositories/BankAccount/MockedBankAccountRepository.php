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
    public const BANK_ACCOUNT_NUMBER_MATCH = 'NL62ABNA9999841479';
    public const BANK_ACCOUNT_NUMBER_NO_MATCH = 'NL12ABNA9999876523';
    public const BANK_ACCOUNT_NUMBER_CLOSE_MATCH = 'NL58ABNA9999142181';
    public const BANK_ACCOUNT_NUMBER_TOO_SHORT = 'NL76ABNA9999161548';
    public const BANK_ACCOUNT_NUMBER_COULD_NOT_MATCH = 'NL04RABO8731326943';

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
            self::BANK_ACCOUNT_NUMBER_MATCH => [
                AccountNumberValidation::Valid,
                NameMatchResult::Match,
                null
            ],
            //NameMatchResult::NoMatch
            self::BANK_ACCOUNT_NUMBER_NO_MATCH => [
                AccountNumberValidation::Valid,
                NameMatchResult::NoMatch,
                null
            ],
            //NameMatchResult::CloseMatch
            self::BANK_ACCOUNT_NUMBER_CLOSE_MATCH => [
                AccountNumberValidation::Valid,
                NameMatchResult::CloseMatch,
                self::BANK_HOLDER_SUGGESTION
            ],
            //NameMatchResult::NameTooShort
            self::BANK_ACCOUNT_NUMBER_TOO_SHORT => [
                AccountNumberValidation::Valid,
                NameMatchResult::NameTooShort,
                null
            ],
            //NameMatchResult::CouldNotMatch
            self::BANK_ACCOUNT_NUMBER_COULD_NOT_MATCH => [
                AccountNumberValidation::Valid,
                NameMatchResult::CouldNotMatch,
                null
            ],
        ];

        return $bankAccountMockData[$accountNumber] ?? [AccountNumberValidation::Valid, NameMatchResult::NoMatch, null];
    }

    public function hasAccountHolderMatchWithSuggestion(string $accountNumber, string $accountHolder): bool
    {
        return $accountNumber === self::BANK_ACCOUNT_NUMBER_CLOSE_MATCH &&
            $accountHolder === self::BANK_HOLDER_SUGGESTION;
    }
}
