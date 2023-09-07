<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Repositories;

use MinVWS\DUSi\Shared\Application\Models\ApplicationMessage;
use MinVWS\DUSi\Shared\Application\Models\Identity;

class ApplicationMessageRepository
{
    public function getMyApplicationMessage(Identity $identity, mixed $id): ApplicationMessage
    {
        return ApplicationMessage::query()->scopes([
            'identity' => $identity,
        ])->find($id);
    }

    public function getMyApplicationMessages(Identity $identity): array
    {
        return ApplicationMessage::query()->scopes([
            'identity' => $identity,
        ])->get()->toArray();
    }
}
