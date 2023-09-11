<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Repositories;

use MinVWS\DUSi\Shared\Application\Models\ApplicationMessage;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Message;

class ApplicationMessageRepository
{
    public function getMyMessage(Identity $identity, mixed $id): ?ApplicationMessage
    {
        $message = $identity->applicationMessages()->find($id);
        /* @phpstan-ignore-next-line */
        assert($message === null || $message instanceof ApplicationMessage);
        return $message;
    }

    /**
     * @return array<ApplicationMessage>
     */
    public function getMyMessages(Identity $identity): array
    {
        /** @var array<ApplicationMessage> $messages */
        $messages = $identity->applicationMessages->toArray();
        return $messages;
    }

    public function createMessage(ApplicationStage $stage, string $htmlPath, string $pdfPath): void
    {
        $stage->application->applicationMessages()->create([
            'html_path' => $htmlPath,
            'is_new' => true,
            'pdf_path' => $pdfPath,
            'subject' => $this->getSubject($stage),
        ]);
    }

    private function getSubject(ApplicationStage $stage): string
    {
        // TODO: verbosity on subject

        return vsprintf('%s', [
            $stage->subsidyStage->subsidyVersion->subsidy->title,
        ]);
    }
}
