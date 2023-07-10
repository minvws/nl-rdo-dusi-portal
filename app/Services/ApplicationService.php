<?php
declare(strict_types=1);

namespace App\Services;

use App\Jobs\ProcessFileUpload;
use App\Jobs\ProcessFormSubmit;
use App\Models\Application;
use App\Models\FormData;
use App\Models\DraftApplication;
use App\Services\Exceptions\ApplicationNotFoundException;
use App\Shared\Models\Application\FileUpload;
use App\Shared\Models\Application\FormSubmit;
use Illuminate\Http\UploadedFile;
use Ramsey\Uuid\Uuid;

readonly class ApplicationService
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

    public function createDraft(FormData $form): string
    {
        $id = Uuid::uuid4()->toString();
        $application = new DraftApplication($id, $form->id);;
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
