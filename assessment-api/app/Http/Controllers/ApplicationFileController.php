<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Response;
use MinVWS\DUSi\Assessment\API\Events\Logging\ViewFileEvent;
use MinVWS\DUSi\Assessment\API\Http\Requests\ApplicationFileUploadRequest;
use MinVWS\DUSi\Assessment\API\Services\ApplicationFileService;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\User\Models\User;
use MinVWS\Logging\Laravel\LogService;

class ApplicationFileController extends Controller
{
    public function __construct(
        private ApplicationFileService $applicationFileService,
        private LogService $logger,
    ) {
    }

    /**
     * @throws AuthorizationException
     */
    public function show(
        Application $application,
        ApplicationStage $applicationStage,
        Field $field,
        string $fileId,
        Authenticatable $user
    ): Response {
        $this->authorize('show', $application);
        assert($user instanceof User);
        $this->logger->log((new ViewFileEvent())
            ->withActor($user)
            ->withData([
                'applicationId' => $application->id,
                'fieldCode' => $field->code,
                'fileId' => $fileId,
                'userId' => $user->getAuthIdentifier(),
            ]));
        return $this->applicationFileService->getApplicationFile(
            $applicationStage,
            $field,
            $fileId
        );
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) Application is used for route binding
     */
    public function uploadFile(
        Application $application,
        ApplicationStage $applicationStage,
        Field $field,
        ApplicationFileUploadRequest $request,
    ): Response {
        return $this->applicationFileService->createApplicationFile(
            $applicationStage,
            $field,
            $request->validated('file')
        );
    }
}
