<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Services\Hsm;

use MinVWS\DUSi\Assessment\API\Services\Hsm\Exceptions\HsmServiceException;
use Exception;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

class HsmService
{
    public function __construct(
        protected ClientInterface $client,
        protected ?string $endpointUrl,
        protected ?string $module,
        protected ?string $slot,
    ) {
    }

    public function decryptHsm(
        ?string $module = null,
        ?string $slot = null,
        ?string $label = null,
        ?string $data = null
    ): string {
        $module = $module ?? $this->module;
        $slot = $slot ?? $this->slot;

        if (empty($module)) {
            throw new HsmServiceException('Module name is required');
        }

        if (empty($slot)) {
            throw new HsmServiceException('Slot name is required');
        }

        if (empty($label)) {
            throw new HsmServiceException('Label is required');
        }

        if (empty($data)) {
            throw new HsmServiceException('Data is required');
        }

        try {
            $response = $this->client->request('POST', $this->endpointUrl . '/' . $module . '/' . $slot .
                '/decrypt', [
                RequestOptions::JSON => [
                    'label' => $label,
                    'objtype' => 'PRIVATE_KEY',
                    'data' => $data,
                ],
                ]);

            $decodesResponse = $this->decodeResponse($response->getBody()->getContents());

            if (empty($decodesResponse) || !isset($decodesResponse['result'])) {
                throw new HsmServiceException('Could not decrypt data');
            }
        } catch (GuzzleException $e) {
            throw new HsmServiceException('Could not decrypt
             data', 0, $e);
        }
        try {
            return base64_decode($decodesResponse['result']);
        } catch (Exception $e) {
            throw new HsmServiceException('Could not decode data', 0, $e);
        }
    }
    protected function decodeResponse(string $response): array
    {
        try {
            $decodedResponse = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new HsmServiceException('Could not decode response from HSM API', 0, $e);
        }

        return $decodedResponse;
    }
}
