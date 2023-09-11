<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Mappers;

use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationMessage;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Application as ApplicationDTO;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationList as ApplicationListDTO;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationListApplication as ApplicationListApplicationDTO;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Message as MessageDTO;

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
            $app->created_at,
            $app->final_review_deadline,
            $app->status,
            in_array($app->status, [ApplicationStatus::Draft, ApplicationStatus::RequestForChanges])
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

    public function mapApplicationToApplicationDTO(Application $app, ?object $data, ?array $files): ApplicationDTO
    {
        $subsidy = $this->subsidyMapper->mapSubsidyVersionToSubsidyDTO($app->subsidyVersion);
        $form = $this->subsidyMapper->mapSubsidyVersionToFormDTO($app->subsidyVersion);

        return new ApplicationDTO(
            $app->reference,
            $subsidy,
            $app->created_at, // TODO: $app->submitted_at,
            $app->final_review_deadline,
            $app->status,
            $app->status->isEditableForApplicant(),
            $form,
            $data,
            $files
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
}
