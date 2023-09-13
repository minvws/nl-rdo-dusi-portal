<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use MinVWS\DUSi\Application\Backend\Repositories\IdentityRepository;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedIdentity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\IdentityType;

class IdentityService
{
    public function __construct(
        private readonly IdentityRepository $identityRepository,
        private readonly EncryptionService $encryptionService,
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
        $identity = $this->findIdentity($encryptedIdentity);
        if ($identity !== null) {
            return $identity;
        }

        $identifier = $this->encryptionService->decryptData($encryptedIdentity->encryptedIdentifier);
        $hashedIdentifier = $this->hashIdentifier($encryptedIdentity->type, $identifier);
        $encryptedIdentifier = $this->encryptionService->encryptData($identifier);

        return $this->identityRepository->createIdentity(
            $encryptedIdentity->type,
            $encryptedIdentifier,
            $hashedIdentifier
        );
    }

    public function findIdentity(EncryptedIdentity $encryptedIdentity): ?Identity
    {
        $identifier = $this->encryptionService->decryptData($encryptedIdentity->encryptedIdentifier);

        return $this->identityRepository->findIdentity(
            type: $encryptedIdentity->type,
            hashedIdentifier: $this->hashIdentifier($encryptedIdentity->type, $identifier)
        );
    }
}
