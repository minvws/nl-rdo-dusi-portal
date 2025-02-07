<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Repositories;

use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\HsmEncryptedData;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\IdentityType;
use Ramsey\Uuid\Uuid;

class IdentityRepository
{
    public function createIdentity(
        IdentityType $type,
        HsmEncryptedData $encryptedIdentifier,
        string $hashedIdentifier
    ): Identity {
        $identity = new Identity();
        $identity->id = Uuid::uuid4()->toString();
        $identity->type = $type;
        $identity->encrypted_identifier = $encryptedIdentifier;
        $identity->hashed_identifier = $hashedIdentifier;
        $identity->save();
        return $identity;
    }

    public function findIdentity(IdentityType $type, string $hashedIdentifier, bool $lockForUpdate = false): ?Identity
    {
        return Identity::query()
                ->when($lockForUpdate, fn ($q) => $q->lockForUpdate())
                ->where('type', $type->value)
                ->where('hashed_identifier', $hashedIdentifier)
                ->first();
    }
}
