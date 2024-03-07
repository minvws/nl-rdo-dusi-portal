<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use MinVWS\DUSi\Application\API\Http\Helpers\ClientPublicKeyHelper;
use MinVWS\DUSi\Application\API\Services\ApplicationService;
use MinVWS\DUSi\Application\API\Services\StateService;
use MinVWS\DUSi\Assessment\API\Events\Logging\ViewFileEvent;
use MinVWS\DUSi\Assessment\API\Services\ApplicationFileService;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\BinaryData;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedApplicationFileUploadParams;
use MinVWS\DUSi\Shared\User\Models\User;
use MinVWS\Logging\Laravel\LogService;
use Ramsey\Uuid\Uuid;

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
        string $applicationStageId,
        string $fieldCode,
        string $fileId,
        Authenticatable $user
    ): Response {
        $this->authorize('show', $application);
        assert($user instanceof User);
        $this->logger->log((new ViewFileEvent())
            ->withActor($user)
            ->withData([
                'applicationId' => $application->id,
                'fieldCode' => $fieldCode,
                'fileId' => $fileId,
                'userId' => $user->getAuthIdentifier(),
            ]));
        return $this->applicationFileService->getApplicationFile(
            $application,
            $applicationStageId,
            $fieldCode,
            $fileId
        );
    }

    public function uploadFile(
        Application $application,
        string $applicationStageId,
        string $fieldCode,
        Request $request,
    ): Response {
        $fileId = Uuid::uuid4()->toString();
        return $this->applicationFileService->writeApplicationFile(
            $application,
            $applicationStageId,
            $fieldCode,
            $fileId,
            $request->getContent()
        );
    }
}
