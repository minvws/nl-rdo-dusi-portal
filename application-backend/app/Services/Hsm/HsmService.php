<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services\Hsm;

use Exception;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use MinVWS\DUSi\Application\Backend\Services\Hsm\Exceptions\HsmServiceException;

/**
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
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

    public function getList(): array
    {
        try {
            $response = $this->client->request('GET', $this->endpointUrl . '/list');
        } catch (GuzzleException $e) {
            throw new HsmServiceException('Could not get list of HSM modules', 0, $e);
        }

        return $this->decodeResponse($response->getBody()->getContents());
    }

    public function getModule(?string $module = null): array
    {
        $module = $module ?? $this->module;

        if (empty($module)) {
            throw new HsmServiceException('Module name is required');
        }

        try {
            $response = $this->client->request('GET', $this->endpointUrl . '/' . $module);
        } catch (GuzzleException $e) {
            throw new HsmServiceException(
                'Could not get list of HSM slots for module: ' . $module,
                0,
                $e
            );
        }

        return $this->decodeResponse($response->getBody()->getContents());
    }


    public function getSlot(?string $module = null, ?string $slot = null): array
    {
        $module = $module ?? $this->module;
        $slot = $slot ?? $this->slot;

        if (empty($module)) {
            throw new HsmServiceException('Module name is required');
        }

        if (empty($slot)) {
            throw new HsmServiceException('Slot name is required');
        }

        try {
            $response = $this->client->request('GET', $this->endpointUrl . '/' . $module . '/' . $slot);
        } catch (GuzzleException $e) {
            throw new HsmServiceException('Could not get list of HSM objects for module: ' . $module .
                ', and slot: ' . $slot, 0, $e);
        }

        return $this->decodeResponse($response->getBody()->getContents());
    }

    public function getObject(
        ?string $module = null,
        ?string $slot = null,
        ?string $objectType = null,
        ?string $label = null
    ): array {
        $module = $module ?? $this->module;
        $slot = $slot ?? $this->slot;

        if (empty($module)) {
            throw new HsmServiceException('Module name is required');
        }

        if (empty($slot)) {
            throw new HsmServiceException('Slot name is required');
        }

        if (!in_array($objectType, ['PRIVATE_KEY', 'PUBLIC_KEY'], true)) {
            throw new HsmServiceException('Object type is required');
        }

        if (empty($label)) {
            throw new HsmServiceException('Label is required');
        }

        try {
            $response = $this->client->request('POST', $this->endpointUrl . '/' . $module . '/' . $slot, [
                RequestOptions::JSON => [
                    'objtype' => $objectType,
                    'label' => $label,
                ],
            ]);
        } catch (GuzzleException $e) {
            throw new HsmServiceException('Could not get object details of object: ' . $objectType .
                ' for module: ' . $module . ', and slot: ' . $slot, 0, $e);
        }

        return $this->decodeResponse($response->getBody()->getContents());
    }

    public function generateRsa(?string $module = null, ?string $slot = null, ?string $label = null): array
    {
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

        try {
            $response = $this->client->request('POST', $this->endpointUrl . '/' . $module . '/' . $slot .
                '/generate/rsa', [
                RequestOptions::JSON => [
                    'label' => $label,
                ],
            ]);
        } catch (BadResponseException $e) {
            $responseBody = '';
            if ($e->hasResponse()) {
                $responseBody = $e->getResponse()->getBody();
            }

            throw new HsmServiceException('Could not generate RSA key for module: ' . $module .
                ', and slot: ' . $slot . ', received: ' . $responseBody, 0, $e);
        } catch (GuzzleException $e) {
            throw new HsmServiceException('Could not generate RSA key for module: ' . $module .
                ', and slot: ' . $slot, 0, $e);
        }

        return $this->decodeResponse($response->getBody()->getContents());
    }

    /**
     * Destroy
     * @param string|null $module
     * @param string|null $slot
     * @param string|null $objectType PRIVATE_KEY|PUBLIC_KEY
     * @param string|null $label
     * @return bool True when the object was destroyed, false when the object was not found or could not be removed.
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function destroyObjectInSlot(
        ?string $module = null,
        ?string $slot = null,
        ?string $objectType = null,
        ?string $label = null
    ): bool {
        $module = $module ?? $this->module;
        $slot = $slot ?? $this->slot;

        if (empty($module)) {
            throw new HsmServiceException('Module name is required');
        }

        if (empty($slot)) {
            throw new HsmServiceException('Slot name is required');
        }

        if (!in_array($objectType, ['PRIVATE_KEY', 'PUBLIC_KEY'], true)) {
            throw new HsmServiceException('Object type is required');
        }

        if (empty($label)) {
            throw new HsmServiceException('Label is required');
        }

        try {
            $response = $this->client->request('POST', $this->endpointUrl . '/' . $module . '/' . $slot . '/destroy', [
                RequestOptions::JSON => [
                    'objtype' => $objectType,
                    'label' => $label,
                ],
            ]);
        } catch (GuzzleException $e) {
            throw new HsmServiceException('Could not destroy object of type: ' . $objectType .
                ' for module: ' . $module . ', and slot: ' . $slot, 0, $e);
        }

        $decodesResponse = $this->decodeResponse($response->getBody()->getContents());

        if (empty($decodesResponse) || !isset($decodesResponse['result'])) {
            return false;
        }

        if (!isset($decodesResponse['result']['removed']) || $decodesResponse['result']['removed'] === 0) {
            return false;
        }

        return true;
    }

    public function importObjectInSlot(
        ?string $module = null,
        ?string $slot = null,
        ?string $objectType = null,
        ?string $label = null,
        ?string $pem = null
    ): array {
        $module = $module ?? $this->module;
        $slot = $slot ?? $this->slot;

        if (empty($module)) {
            throw new HsmServiceException('Module name is required');
        }

        if (empty($slot)) {
            throw new HsmServiceException('Slot name is required');
        }

        if (!in_array($objectType, ['PRIVATE_KEY', 'PUBLIC_KEY'], true)) {
            throw new HsmServiceException('Object type is required');
        }
        if (empty($pem)) {
            throw new HsmServiceException('Pem is required');
        }

        try {
            $response = $this->client->request('POST', $this->endpointUrl . '/' . $module . '/' . $slot . '/import', [
                RequestOptions::JSON => [
                    'objtype' => $objectType,
                    'label' => $label,
                    'data' => base64_encode($pem),
                    'pem' => true,
                ],
            ]);
        } catch (GuzzleException $e) {
            throw new HsmServiceException('Could not import object of type: ' . $objectType .
                ' for module: ' .
                $module . ', and slot: ' . $slot, 0, $e);
        }

        return $this->decodeResponse($response->getBody()->getContents());
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
