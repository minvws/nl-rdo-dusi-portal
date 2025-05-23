<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Repositories;

use MinVWS\DUSi\Shared\Application\Models\ApplicationMessage;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStageTransition;
use MinVWS\DUSi\Shared\Application\Models\Identity;

class ApplicationMessageRepository
{
    public function getMyMessage(Identity $identity, mixed $id): ?ApplicationMessage
    {
        $message = $identity->applicationMessages()->find($id);
        assert($message === null || $message instanceof ApplicationMessage);

        if ($message?->is_new) {
            $message->is_new = false;

            $message->save();
        }

        return $message;
    }

    /**
     * @return array<ApplicationMessage>
     */
    public function getMyMessages(Identity $identity): array
    {
        /** @var array<ApplicationMessage> $result */
        $result = $identity->applicationMessages->all();
        return $result;
    }

    public function getMyMessagesCount(Identity $identity): int
    {
        return $identity->applicationMessages()->count();
    }

    public function getMyUnreadMessagesCount(Identity $identity): int
    {
        return $identity->applicationMessages()->where('is_new', '=', true)->count();
    }

    public function createMessage(
        ApplicationStageTransition $transition,
        string $subject,
        string $htmlPath,
        string $pdfPath
    ): ApplicationMessage {
        $message = $transition->application->applicationMessages()->make();
        $message->applicationStageTransition()->associate($transition);
        $message->subject = $subject;
        $message->html_path = $htmlPath;
        $message->pdf_path = $pdfPath;
        $message->is_new = true;
        $message->save();
        return $message;
    }
}
