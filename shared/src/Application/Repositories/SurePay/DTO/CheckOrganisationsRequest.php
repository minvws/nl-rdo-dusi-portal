<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\Exceptions\SurePayRepositoryException;

class CheckOrganisationsRequest
{
    public array $accountId;
    public string $name;

    public function __construct(array $request)
    {
        $this->throwIfInvalid($request);

        $this->accountId = $request['accountId'];
        $this->name = $request['name'];
    }

    /**
     * @param string $acountOwner
     * Name of the account holder. Mandatory when not using at least 1 type of companyId.
     * The max. length - 140 characters.
     * @param string $accountNumber
     *  The identifier of the account to be checked. The max allowed length depends on the type.
     *  [A-Z]{2,2}[0-9]{2,2}[a-zA-Z0-9]{1,30} (For IBAN, just capital letters are accepted, without white spaces).
     * @param string $accountType
     * Describes the type of the account that needs to be found. For now it can be IBAN, SortCodeAccountNumber,
     * AccountNumber, ShortenedAccount, Email or Phone. Even though there are many types available in the model,
     * only checks with IBAN'S are functionally available for the EU corporate market.
     * @return CheckOrganisationsRequest
     */
    public static function build(
        string $acountOwner,
        string $accountNumber,
        string $accountType = 'IBAN'
    ): CheckOrganisationsRequest {
        return new self([
            'accountId' => [
                'value' => $accountNumber,
                'type' => $accountType
            ],
            'name' => $acountOwner
        ]);
    }

    /**
     * @return CheckOrganisationsRequest
     */
    public function toArray(): CheckOrganisationsRequest
    {
        return new self([
            'accountId' => $this->accountId,
            'name' => $this->name
        ]);
    }

    /**
     * @param array $request
     * @return void
     * @throws SurePayRepositoryException
     */
    private function throwIfInvalid(array $request): void
    {
        try {
            Validator::make($request, [
                'accountId' => 'required|array',
                'accountId.value' => 'required|regex:/^[A-Z]{2}[0-9]{2}[a-zA-Z0-9]{1,30}$/',
                'accountId.type' => 'required|string',
                'name' => 'required|max:140',
            ], [
                'accountId.value.regex' => 'The :attribute format is invalid. It should match the given pattern.',
            ])->validate();
        } catch (ValidationException $e) {
            throw new SurePayRepositoryException('Request validation failed: ', 0, $e);
        }
    }
}
