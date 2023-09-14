<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Services;

use Config;
use Exception;
use Illuminate\Auth\AuthenticationException;
use MinVWS\DUSi\Shared\Bridge\Client\Client;
use MinVWS\DUSi\Shared\Serialisation\Jobs\ProcessFileUpload;
use MinVWS\DUSi\Shared\Serialisation\Jobs\ProcessFormSubmit;
use MinVWS\DUSi\Application\API\Models\Application;
use MinVWS\DUSi\Application\API\Models\SubsidyStageData;
use MinVWS\DUSi\Application\API\Models\DraftApplication;
use MinVWS\DUSi\Application\API\Services\Exceptions\ApplicationNotFoundException;
use Illuminate\Http\UploadedFile;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationFindOrCreateParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationListParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedApplicationFileUploadParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedApplicationSaveParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedIdentity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\FileUpload;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\FormSubmit;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\RPCMethods;
use MinVWS\DUSi\Application\API\Services\Exceptions\DataEncryptionException;
use Ramsey\Uuid\Uuid;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ApplicationService
{
    public function __construct(
        private readonly StateService $stateService,
        private readonly Client $bridgeClient,
        private EncryptionService $encryptionService
    ) {
    }

    /**
     * @throws ApplicationNotFoundException
     */
    public function getDraftApplication(string $id): ?DraftApplication
    {
        $application = $this->stateService->getDraftApplication($id);
        if ($application === null) {
            throw new ApplicationNotFoundException();
        }
        return $application;
    }

    public function createDraft(SubsidyStageData $subsidyStageData): string
    {
        $id = Uuid::uuid4()->toString();
        $application = new DraftApplication($id, $subsidyStageData->id);
        $this->stateService->registerDraftApplication($application);
        return $application->id;
    }

    /**
     * @throws AuthenticationException
     * @throws DataEncryptionException
     * @throws Exception
     */
    public function uploadFile(Application $application, string $fieldCode, UploadedFile $file): string
    {
        $id = Uuid::uuid4()->toString();

        $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
        if (empty($extension)) {
            $extension = null;
        }

        // TODO: remove encryption here when frontend implements encryption

        $encryptedFile = Config::get('encryption.encrypt_till_support') ?
            $this->encryptionService->encryptData($file->getContent()) : $file->getContent();

        if ($file->getMimeType() === null) {
            throw new Exception('Mime type is null');
        }

        // TEMP WORKAROUND UNTIL THIS CODE IS REMOVED
        $identity = $this->stateService->getEncryptedIdentity();
        $fileUpload = new FileUpload(
            identity: new EncryptedIdentity($identity->type, base64_encode($identity->encryptedIdentifier)),
            applicationMetadata: $application->getMetadata(),
            fieldCode: $fieldCode,
            id: $id,
            mimeType: $file->getMimeType(),
            extension: $extension,
            encryptedContents: $encryptedFile
        );

        ProcessFileUpload::dispatch($fileUpload);

        return $id;
    }

    /**
     * @throws AuthenticationException
     * @throws DataEncryptionException
     */
    public function submit(Application $application, string $formData): void
    {
        // TEMP WORKAROUND UNTIL THIS CODE IS REMOVED
        $identity = $this->stateService->getEncryptedIdentity();
        $formSubmit = new FormSubmit(
            identity: new EncryptedIdentity($identity->type, base64_encode($identity->encryptedIdentifier)),
            applicationMetadata: $application->getMetadata(),
            encryptedData: $formData
        );

        ProcessFormSubmit::dispatch($formSubmit);
    }

    /**
     * @throws Exception
     */
    public function listApplications(ApplicationListParams $params): EncryptedResponse
    {
        return $this->bridgeClient->call(RPCMethods::LIST_APPLICATIONS, $params, EncryptedResponse::class);
    }

    /**
     * @throws Exception
     */
    public function getApplication(ApplicationParams $params): EncryptedResponse
    {
        return $this->bridgeClient->call(RPCMethods::GET_APPLICATION, $params, EncryptedResponse::class);
    }

    public function findOrCreateApplication(ApplicationFindOrCreateParams $params): EncryptedResponse
    {
        return $this->bridgeClient->call(RPCMethods::FIND_OR_CREATE_APPLICATION, $params, EncryptedResponse::class);
    }

    public function uploadApplicationFile(EncryptedApplicationFileUploadParams $params): EncryptedResponse
    {
        return $this->bridgeClient->call(RPCMethods::UPLOAD_APPLICATION_FILE, $params, EncryptedResponse::class);
    }

    public function saveApplication(EncryptedApplicationSaveParams $params): EncryptedResponse
    {
        return $this->bridgeClient->call(RPCMethods::SAVE_APPLICATION, $params, EncryptedResponse::class);
    }
}
