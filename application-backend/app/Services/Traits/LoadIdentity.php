<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services\Traits;

use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Serialisation\Exceptions\EncryptedResponseException;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedIdentity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;

trait LoadIdentity
{
    /**
     * @throws EncryptedResponseException
     */
    private function loadIdentity(EncryptedIdentity $encryptedIdentity): Identity
    {
        $identity = $this->identityService->findIdentity($encryptedIdentity);
        if ($identity !== null) {
            return $identity;
        }

        throw new EncryptedResponseException(
            EncryptedResponseStatus::NOT_FOUND,
            'identity_not_found',
            'Identity not registered yet.'
        );
    }
}
