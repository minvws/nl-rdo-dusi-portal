<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Services;

use MinVWS\DUSi\Shared\Serialisation\Jobs\ProcessFileUpload;
use MinVWS\DUSi\Shared\Serialisation\Jobs\ProcessFormSubmit;
use MinVWS\DUSi\Application\API\Models\Application;
use MinVWS\DUSi\Application\API\Models\SubsidyStageData;
use MinVWS\DUSi\Application\API\Models\DraftApplication;
use MinVWS\DUSi\Application\API\Services\Exceptions\ApplicationNotFoundException;
use Illuminate\Http\UploadedFile;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\FileUpload;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\FormSubmit;
use Ramsey\Uuid\Uuid;

class ApplicationService
{
    public function __construct(
        private StateService $stateService
    ) {
    }

    /**
     * @throws ApplicationNotFoundException
     */
    public function getApplication(string $id): ?DraftApplication
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

    public function uploadFile(Application $application, string $fieldCode, UploadedFile $file): string
    {
        $id = Uuid::uuid4()->toString();

        $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
        if (empty($extension)) {
            $extension = null;
        }

        $encryptedContents = $file->getContent();

        if ($file->getMimeType() === null) {
            throw new \Exception('Mime type is null');
        }

        $fileUpload = new FileUpload(
            identity: $this->stateService->getIdentity(),
            applicationMetadata: $application->getMetadata(),
            fieldCode: $fieldCode,
            id: $id,
            mimeType: $file->getMimeType(),
            extension: $extension,
            encryptedContents: base64_encode($encryptedContents)
        );

        ProcessFileUpload::dispatch($fileUpload);

        return $id;
    }

    public function submit(Application $application, string $encryptedData): void
    {
        $formSubmit = new FormSubmit(
            identity: $this->stateService->getIdentity(),
            applicationMetadata: $application->getMetadata(),
            encryptedData: base64_encode($encryptedData)
        );

        ProcessFormSubmit::dispatch($formSubmit);
    }
}
