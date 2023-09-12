<?php
declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services\SurePay\DTO;

use Illuminate\Support\Facades\Log;

class CheckOrganisationsResponse
{
    /**
     * @var string $nameMatchResult Describes the result of the account name check:
     *  - MATCH - when the provided name matches the value of the account holder name held by the source.
     *  - CLOSE_MATCH - when the provided name closely resembles the value of the account holder name held by the source.
     *  - NO_MATCH - when the provided name does not match the value of the account holder name held by the source.
     *  - COULD_NOT_MATCH - when the provided name could not be matched against the source data. This could have several reasons:
     *    - We do not have the data of this specific bank account in order to successfully do the matching
     *    - There is no name provided in the input, so there is no input name to match against
     *  - NAME_TOO_SHORT- when the provided name is too short to perform a match against the value of the account holder name held
     *  by the source.
     */
     public string $nameMatchResult;

     /**
     * @var string $dataUsedForMatching
     * Describes the data used to reach the match result:
     * - VERIFIED : Verified is the data that exists at the beneficiary bank.
     * - DERIVED: Derived is data based on historical transactions.
     */
    public string $dataUsedForMatching;
    /**
     * @var CheckOrganisationsAccountResponse
     * Object holding the following bank account details.
     */
    public CheckOrganisationsAccountResponse $account;
    /**
     * @var string The supported platforms used for confirmation of payee:
     * SWIFT, COP_UK, SEPAMAIL, SurePay, Liink.az13
     */
    public string $scheme;

    public function __construct($response)
    {
        Log::debug($response);

        $this->nameMatchResult = $response['nameMatchResult'];
        $this->dataUsedForMatching = $response['dataUsedForMatching'];
        $this->account = new CheckOrganisationsAccountResponse($response['account']);
        $this->scheme = $response['scheme'];
    }

    public static function fromArray($arrayData): CheckOrganisationsResponse
    {
        return new self($arrayData);
    }
}
