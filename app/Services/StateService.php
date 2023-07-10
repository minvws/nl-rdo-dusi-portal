<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\DraftApplication;
use App\Shared\Models\Application\Identity;
use App\Shared\Models\Application\IdentityType;
use Illuminate\Session\SessionManager;

readonly class StateService
{
    private const KEY_APPLICATION = 'application.%s';
    private const KEY_FORM_ID = 'formId';

    public function __construct(private SessionManager $sessionManager)
    {
    }

    public function getIdentity(): Identity
    {
        // TODO: as the DigiD login (or other types of logins) are not implemented yet we currently fake the identity
        return new Identity(
            IdentityType::EncryptedCitizenServiceNumber,
            base64_encode(openssl_random_pseudo_bytes(32))
        );
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
