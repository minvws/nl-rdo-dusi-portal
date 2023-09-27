<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use MinVWS\DUSi\Application\Backend\Repositories\IdentityRepository;
use MinVWS\DUSi\Shared\Application\Services\Hsm\HsmDecryptionService;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedIdentity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\IdentityType;

class IdentityService
{
    public function __construct(
        private readonly IdentityRepository $identityRepository,
        private readonly HsmDecryptionService $decryptionService,
        private readonly string $hashSecret,
        private readonly string $hashAlgorithm = 'sha256'
    ) {
    }

    public function hashIdentifier(IdentityType $type, string $identifier): string
    {
        $input = implode('|', [$type->value, $identifier]);
        return hash_hmac($this->hashAlgorithm, $input, $this->hashSecret);
    }

    public function findOrCreateIdentity(EncryptedIdentity $encryptedIdentity): Identity
    {
        $identifier = $this->decryptionService->decrypt($encryptedIdentity->encryptedIdentifier);
        $hashedIdentifier = $this->hashIdentifier($encryptedIdentity->type, $identifier);

        $identity = $this->findIdentityByIdentifier($encryptedIdentity->type, $hashedIdentifier);
        if ($identity !== null) {
            return $identity;
        }

        return $this->identityRepository->createIdentity(
            $encryptedIdentity->type,
            $encryptedIdentity->encryptedIdentifier,
            $hashedIdentifier
        );
    }

    public function findIdentity(EncryptedIdentity $encryptedIdentity): ?Identity
    {
        $identifier = $this->decryptionService->decrypt($encryptedIdentity->encryptedIdentifier);
        $hashedIdentifier = $this->hashIdentifier($encryptedIdentity->type, $identifier);

        return $this->findIdentityByIdentifier($encryptedIdentity->type, $hashedIdentifier);
    }

    protected function findIdentityByIdentifier(IdentityType $type, string $hashedIdentifier): ?Identity
    {
        return $this->identityRepository->findIdentity(
            type: $type,
            hashedIdentifier: $hashedIdentifier,
        );
    }
}
