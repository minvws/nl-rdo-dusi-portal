<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Services;

use MinVWS\DUSi\Application\API\Models\DraftApplication;
use MinVWS\DUSi\Application\API\Models\PortalUser;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\AuthManager;
use Illuminate\Session\SessionManager;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Identity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\IdentityType;

class StateService
{
    private const KEY_APPLICATION = 'application.%s';
    private const KEY_FORM_ID = 'formId';

    public function __construct(
        private SessionManager $sessionManager,
        private AuthManager $authManager,
        private EncryptionService $encryptionService
    ) {
    }

    /**
     * @throws AuthenticationException
     */
    public function getIdentity(): Identity
    {
        $user = $this->authManager->user();

        if ($user instanceof PortalUser) {
            return new Identity(
                IdentityType::EncryptedCitizenServiceNumber,
                $this->encryptionService->encryptData($user->bsn)
            );
        }

        throw new AuthenticationException('User not authenticated!');
    }

    public function getDraftApplication(string $id): ?DraftApplication
    {
        $data = $this->sessionManager->get(sprintf(self::KEY_APPLICATION, $id));
        if ($data === null) {
            return null;
        }

        return new DraftApplication($id, $data[self::KEY_FORM_ID]);
    }

    public function registerDraftApplication(DraftApplication $application): void
    {
        $this->sessionManager->put(
            sprintf(self::KEY_APPLICATION, $application->id),
            [self::KEY_FORM_ID => $application->formId]
        );
    }
}
