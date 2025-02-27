<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Services;

use MinVWS\DUSi\Application\API\Models\PortalUser;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\AuthManager;
use MinVWS\DUSi\Shared\Application\Services\Hsm\HsmEncryptionService;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedIdentity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\IdentityType;

class StateService
{
    public function __construct(
        private AuthManager $authManager,
        private HsmEncryptionService $encryptionService,
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
                type: IdentityType::CitizenServiceNumber,
                encryptedIdentifier: $this->encryptionService->encrypt($user->bsn)
            );
        }

        throw new AuthenticationException('User not authenticated!');
    }
}
