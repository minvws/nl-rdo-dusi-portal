<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Repositories;

use Illuminate\Support\Collection;
use MinVWS\DUSi\Shared\Application\Models\ApplicationMessage;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedIdentity;

class ApplicationMessageRepository
{
    public function getMyApplicationMessage(EncryptedIdentity $identity, mixed $id): ApplicationMessage
    {
        return ApplicationMessage::query()->scopes([
            'encryptedIdentity' => $identity,
        ])->find($id);
    }

    public function getMyApplicationMessages(EncryptedIdentity $identity): array
    {
        return ApplicationMessage::query()->scopes([
            'encryptedIdentity' => $identity,
        ])->get()->toArray();
    }
}
