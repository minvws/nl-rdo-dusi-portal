<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO;

use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\Exceptions\SurePayInvalidAccessTokenResponse;

class AccessTokenResponse
{
    public int $refreshTokenExpiresIn;
    public string $apiProductList;
    public array $apiProductListJson;
    public string $organizationName;
    public string $tokenType;
    public int $issuedAt;
    public string $clientId;
    public string $accessToken;
    public string $applicationName;
    public string $scope;
    public int $expiresIn;
    public int $refreshCount;
    public string $status;

    /**
     * @param $response
     */
    public function __construct(array $response)
    {
        $this->throwIfInvalid($response);

        $this->refreshTokenExpiresIn = (int)$response['refresh_token_expires_in'];
        $this->apiProductList = $response['api_product_list'];
        $this->apiProductListJson = $response['api_product_list_json'];
        $this->organizationName = $response['organization_name'];
        $this->tokenType = $response['token_type'];
        $this->issuedAt = (int)$response['issued_at'];
        $this->clientId = $response['client_id'];
        $this->accessToken = $response['access_token'];
        $this->applicationName = $response['application_name'];
        $this->scope = $response['scope'];
        $this->expiresIn = (int)$response['expires_in'];
        $this->refreshCount = (int)$response['refresh_count'];
        $this->status = $response['status'];
    }

    /**
     * @throws SurePayInvalidAccessTokenResponse
     */
    public static function fromJson(string $jsonResponse): AccessTokenResponse
    {
        if (empty($jsonResponse)) {
            throw new SurePayInvalidAccessTokenResponse('Empty response from SurePay');
        }

        try {
            $data = json_decode($jsonResponse, true, 512, JSON_THROW_ON_ERROR);
            return new self($data);
        } catch (Exception $e) {
            throw new SurePayInvalidAccessTokenResponse(
                message: 'Invalid response from SurePay',
                previous: $e,
            );
        }
    }

    /**
     * @throws ValidationException
     */
    private function throwIfInvalid(array $response): void
    {
        Validator::make($response, [
            'refresh_token_expires_in' => 'required|integer',
            'api_product_list' => 'required',
            'api_product_list_json' => 'required|array',
            'organization_name' => 'required',
            'token_type' => 'required',
            'issued_at' => 'required|integer',
            'client_id' => 'required',
            'access_token' => 'required',
            'application_name' => 'required',
            'scope' => '',
            'expires_in' => 'required|integer',
            'refresh_count' => 'required|integer',
            'status' => 'required',
        ])->validate();
    }
}
