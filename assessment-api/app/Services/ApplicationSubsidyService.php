<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Services;

use Illuminate\Support\Facades\Log;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationSubsidyVersionResource;
use MinVWS\DUSi\Assessment\API\Http\Resources\ApplicationTransitionPreviewResource;
use MinVWS\DUSi\Assessment\API\Services\Exceptions\TransitionNotFoundException;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Services\ApplicationDataService;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFlowService;
use MinVWS\DUSi\Shared\Application\Services\Hsm\HsmDecryptionService;
use MinVWS\DUSi\Shared\Application\Services\LetterService;
use MinVWS\DUSi\Shared\Subsidy\Helpers\SubsidyStageDataSchemaBuilder;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\EvaluationTrigger;
use Throwable;

readonly class ApplicationSubsidyService
{
    public function __construct(
        private ApplicationDataService $applicationDataService,
        private SubsidyStageDataSchemaBuilder $dataSchemaBuilder,
        private ApplicationFlowService $applicationFlowService,
        private LetterService $letterService,
        private HsmDecryptionService $hsmDecryptionService
    ) {
    }

    private function getCitizenServiceNumber(Application $application): string
    {
        try {
            return $this->hsmDecryptionService->decrypt($application->identity->encrypted_identifier);
        } catch (Throwable $e) {
            Log::error('Error trying to decrypt citizen service number: ' . $e->getMessage());
            return 'Onbekend';
        }
    }

    public function getApplicationSubsidyResource(
        Application $application,
        bool $readOnly
    ): ApplicationSubsidyVersionResource {
        return new ApplicationSubsidyVersionResource(
            $application,
            $readOnly,
            $this->getCitizenServiceNumber($application),
            $this->applicationDataService,
            $this->dataSchemaBuilder
        );
    }

    public function getApplicationTransitionPreview(Application $application): ApplicationTransitionPreviewResource
    {
        $stage = $application->currentApplicationStage;
        if ($stage === null) {
            Log::error('No transition found for application with no current stage ' . $application->id);
            throw new TransitionNotFoundException();
        }

        $transition =
            $this->applicationFlowService
                ->evaluateTransitionsForApplicationStage($stage, EvaluationTrigger::Submit);
        if ($transition === null) {
            Log::error('No matching transition found for application ' . $application->id);
            throw new TransitionNotFoundException();
        }

        $messageHtml = null;
        if ($transition->send_message && $transition->publishedSubsidyStageTransitionMessage !== null) {
            $messageHtml = $this->letterService->generatePreview(
                $transition->publishedSubsidyStageTransitionMessage,
                $stage
            );
        }

        return new ApplicationTransitionPreviewResource(
            $application,
            $transition,
            $this->getCitizenServiceNumber($application),
            $messageHtml,
            $this->applicationDataService,
            $this->dataSchemaBuilder
        );
    }
}
