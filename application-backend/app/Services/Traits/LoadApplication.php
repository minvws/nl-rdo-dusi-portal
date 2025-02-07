<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services\Traits;

use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Serialisation\Exceptions\EncryptedResponseException;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;

trait LoadApplication
{
    /**
     * @throws EncryptedResponseException
     */
    private function loadApplication(Identity $identity, string $reference, bool $lockForUpdate = false): Application
    {
        $app = $this->applicationRepository->getMyApplication($identity, $reference, $lockForUpdate);
        if ($app !== null) {
            return $app;
        }

        throw new EncryptedResponseException(
            EncryptedResponseStatus::NOT_FOUND,
            'application_not_found'
        );
    }
}
