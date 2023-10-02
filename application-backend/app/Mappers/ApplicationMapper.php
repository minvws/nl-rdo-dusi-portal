<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Mappers;

use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationMessage;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Application as ApplicationDTO;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationList as ApplicationListDTO;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationListApplication as ApplicationListApplicationDTO;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Message as MessageDTO;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageList as MessageListDTO;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageListMessage as MessageListMessageDTO;

class ApplicationMapper
{
    public function __construct(
        private readonly SubsidyMapper $subsidyMapper
    ) {
    }

    private function mapApplicationToApplicationListApplicationDTO(Application $app): ApplicationListApplicationDTO
    {
        $subsidy = $this->subsidyMapper->mapSubsidyVersionToSubsidyDTO($app->subsidyVersion);

        return new ApplicationListApplicationDTO(
            $app->reference,
            $subsidy,
            $app->submitted_at,
            $app->final_review_deadline,
            $app->status,
            $app->status->isEditableForApplicant(),
        );
    }

    /**
     * @param array<Application> $applications
     */
    public function mapApplicationArrayToApplicationListDTO(array $applications): ApplicationListDTO
    {
        $apps = array_map(
            fn (Application $app) => $this->mapApplicationToApplicationListApplicationDTO($app),
            $applications
        );
        return new ApplicationListDTO($apps);
    }

    public function mapApplicationToApplicationDTO(Application $app, ?object $data): ApplicationDTO
    {
        $subsidy = $this->subsidyMapper->mapSubsidyVersionToSubsidyDTO($app->subsidyVersion);
        $form = $this->subsidyMapper->mapSubsidyVersionToFormDTO($app->subsidyVersion);

        return new ApplicationDTO(
            $app->reference,
            $subsidy,
            $app->submitted_at,
            $app->final_review_deadline,
            $app->status,
            $app->status->isEditableForApplicant(),
            $form,
            $data
        );
    }

    public function mapApplicationMessageToMessageDTO(ApplicationMessage $message, string $body): MessageDTO
    {
        return new MessageDTO(
            $message->id,
            $message->subject,
            $message->sent_at,
            $message->is_new,
            $body
        );
    }

    public function mapApplicationMessageArrayToMessageListDTO(array $applicationMessages): MessageListDTO
    {
        $messages = array_map(
            fn (ApplicationMessage $message) => new MessageListMessageDTO(
                $message->id,
                $message->subject,
                $message->sent_at,
                $message->is_new,
            ),
            $applicationMessages
        );

        return new MessageListDTO($messages);
    }
}
