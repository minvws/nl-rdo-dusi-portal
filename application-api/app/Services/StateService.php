<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Services;

use MinVWS\DUSi\Application\API\Models\PortalUser;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\AuthManager;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedIdentity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\IdentityType;

class StateService
{
    public function __construct(
        private AuthManager $authManager,
        private EncryptionService $encryptionService
    ) {
    }

    /**
     * @throws AuthenticationException
     */
    public function getEncryptedIdentity(): EncryptedIdentity
    {
        $user = $this->authManager->user();

        if ($user instanceof PortalUser) {
            return new EncryptedIdentity(
                IdentityType::CitizenServiceNumber,
                base64_decode($this->encryptionService->encryptData($user->bsn))
            );
        }

        throw new AuthenticationException('User not authenticated!');
    }
}
