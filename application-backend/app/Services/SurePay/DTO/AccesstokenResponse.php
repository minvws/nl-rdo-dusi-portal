<?php

namespace MinVWS\DUSi\Application\Backend\Services\SurePay\DTO;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AccesstokenResponse
{
    public int $refresh_token_expires_in;
    public string $api_product_list;
    public array $api_product_list_json;
    public string $organization_name;
    public string $token_type;
    public int $issued_at;
    public string $client_id;
    public string $access_token;
    public string $application_name;
    public string $scope;
    public int $expires_in;
    public int $refresh_count;
    public string $status;

    /**
     * @throws ValidationException
     */
    public function __construct($response)
    {
        Log::debug($response);
        $this->throwIfInvalid($response);

        $this->refresh_token_expires_in = $response['refresh_token_expires_in'];
        $this->api_product_list = $response['api_product_list'];
        $this->api_product_list_json = $response['api_product_list_json'];
        $this->organization_name = $response['organization_name'];
        $this->token_type = $response['token_type'];
        $this->issued_at = $response['issued_at'];
        $this->client_id = $response['client_id'];
        $this->access_token = $response['access_token'];
        $this->application_name = $response['application_name'];
        $this->scope = $response['scope'];
        $this->expires_in = $response['expires_in'];
        $this->refresh_count = $response['refresh_count'];
        $this->status = $response['status'];
    }

    /**
     * @throws ValidationException
     */
    public static function fromJson($jsonResponse): AccesstokenResponse
    {
        Log::debug($jsonResponse);
        $data = json_decode($jsonResponse, true);

        return new self($data);
    }

    /**
     * @throws ValidationException
     */
    private function throwIfInvalid($response): void
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
