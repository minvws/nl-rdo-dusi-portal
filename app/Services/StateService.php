<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\DraftApplication;
use App\Models\PortalUser;
use App\Shared\Models\Application\Identity;
use App\Shared\Models\Application\IdentityType;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\AuthManager;
use Illuminate\Session\SessionManager;

readonly class StateService
{
    private const KEY_APPLICATION = 'application.%s';
    private const KEY_FORM_ID = 'formId';

    public function __construct(private SessionManager $sessionManager, private AuthManager $authManager)
    {
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
                $user->bsn
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
