<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\SurePay\DTO;

/**
 * Object holding the following bank account details.
 *
 */
class CheckOrganisationsAccountResponse
{
    /**
     * @var string $accountNumberValidation The account validity as determined by SurePay. The possible values are:
     * - VALID - An account that conforms to the standards, e.g. a valid Mod97 calculation for an IBAN.
     * - NOT_VALID - account is an account that does not conform to the standards.
     */
    public string $accountNumberValidation;

    /**
     * @var string $paymentPreValidation Returns the pre-validation status of the payment.Only applicable for Surepay
     * Scheme.
     * - PASS - is returned when the account identification was successfully validated to an account that can receive
     * funds.
     * - WILL_FAIL - is returned if the payment will definitely fail.
     * - WARNING - is returned in case the account identification was not successfully validated to an account that can
     * receive funds, however, the responding bank is unable to provide a definitive answer.
     */
    public string $paymentPreValidation;

    /**
     * @var string $status The status of the account.
     * - ACTIVE account is a valid account and supported for checks.
     * - INACTIVE account is a valid account marked by the account holding bank as inactive.
     * - NOT_SUPPORTED account status stands for an account that is valid but is not supported to perform any checks.
     * - NOT_FOUND account status stands for an account that is valid but could not be found in any of the connected
     * data sources.
     * - UNKNOWN account status is for an account that is either found as part of DERIVED data or a NOT_VALID account.
     */
    public string $status;

    /**
     * @var string $accountType the type of the account holder. The possible values are:
     * - NP - The bank account holder is a Natural Person
     * - ORG - The bank account holder is an organisation
     * - UNKNOWN - The bank account holder is an unknown to us
     * If the ‘accountType’ is provided in the request then the response doesn’t disclose the ‘accountType'.
     * It only returns the 'accountTypeMatchResult’.
     */
    public string $accountType;

    /**
     * @var bool Indicates whether there is more than one account holder associated with the checked account.
     * True if there is more than 1 account holder. Otherwise, it’s retrieved as False.
     * Only returned in NL and when nameMatchResult is a MATCH or CLOSE_MATCH
     */
    public bool $jointAccount;

    /**
     * @var int Contains the number of account holders.
     * Only returned in NL and when nameMatchResult is a MATCH or CLOSE_MATCH
     */
    public int $numberOfAccountHolders;

    /**
     * @var string 'Two letter' country code from the IBAN or derived based on account Id type. In ISO 3166-1
     * alpha-2 format.
     */
    public string $countryCode;

    public function __construct(array $response)
    {
        $this->accountNumberValidation = $response['accountNumberValidation'];
        $this->paymentPreValidation = $response['paymentPreValidation'];
        $this->status = $response['status'];
        $this->accountType = $response['accountType'];
        $this->jointAccount = $response['jointAccount'];
        $this->numberOfAccountHolders = $response['numberOfAccountHolders'];
        $this->countryCode = $response['countryCode'];
    }

    public static function fromJson(string $jsonResponse): CheckOrganisationsAccountResponse
    {
        $decoded = json_decode($jsonResponse, true);

        return new self($decoded);
    }
}
