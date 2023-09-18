<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Services\Hsm;

use MinVWS\DUSi\Shared\Serialisation\Hsm\HsmDecryptableData;

class HsmEncryptionService
{
    public function __construct(
        protected HsmService $hsmService,
    ) {
    }

    public function decrypt(HsmDecryptableData $encryptedData): string
    {
        // TODO: Move to Shared package

        return $this->hsmService->decrypt(
            label: $encryptedData->getKeyLabel(),
            data: $encryptedData->getData(),
        );
    }
}
