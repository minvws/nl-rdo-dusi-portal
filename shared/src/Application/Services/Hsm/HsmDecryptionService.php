<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\Hsm;

use MinVWS\DUSi\Shared\Serialisation\Hsm\HsmDecryptableData;

class HsmDecryptionService
{
    public function __construct(
        protected HsmService $hsmService,
    ) {
    }

    public function decrypt(HsmDecryptableData $encryptedData): string
    {
        return $this->hsmService->decrypt(
            label: $encryptedData->getKeyLabel(),
            data: $encryptedData->getData(),
        );
    }
}
