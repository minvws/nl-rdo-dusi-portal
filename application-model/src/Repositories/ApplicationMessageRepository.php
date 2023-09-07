<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Repositories;

use MinVWS\DUSi\Shared\Application\Models\ApplicationMessage;
use MinVWS\DUSi\Shared\Application\Models\Identity;

class ApplicationMessageRepository
{
    public function getMyApplicationMessage(Identity $identity, mixed $id): ?ApplicationMessage
    {
        $message = ApplicationMessage::forIdentity($identity)->find($id);
        assert($message === null || $message instanceof ApplicationMessage);
        return $message;
    }

    public function getMyApplicationMessages(Identity $identity): array
    {
        return ApplicationMessage::forIdentity($identity)->get()->toArray();
    }
}
