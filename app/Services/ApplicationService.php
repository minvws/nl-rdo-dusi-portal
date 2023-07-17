<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Application;
use App\Models\ApplicationStatus;
use App\Models\Disk;
use App\Repositories\FormRepository;
use App\Services\Exceptions\ApplicationIdentityMismatchException;
use App\Services\Exceptions\ApplicationMetadataMismatchException;
use App\Services\Exceptions\FieldNotFoundException;
use App\Services\Exceptions\FieldTypeMismatchException;
use App\Services\Exceptions\FileNotFoundException;
use App\Services\Exceptions\FormNotFoundException;
use App\Shared\Models\Application\ApplicationMetadata;
use App\Shared\Models\Application\FileUpload;
use App\Shared\Models\Application\FormSubmit;
use App\Shared\Models\Application\Identity;
use App\Shared\Models\Connection;
use App\Models\Submission\FieldValue;
use App\Repositories\ApplicationRepository;
use App\Shared\Models\Definition\Field;
use App\Shared\Models\Definition\FieldType;
use App\Shared\Models\Definition\Form;
use Exception;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\Facades\DB;
use Throwable;

readonly class ApplicationService
{
    public function __construct(
        private FormRepository $formRepository,
        private FormDecodingService $decodingService,
        private EncryptionService $encryptionService,
        private ApplicationRepository $applicationRepository,
        private FilesystemManager $filesystemManager
    ) {
    }

    private function loadApplicationIfExists(string $applicationId): ?Application
    {
        return $this->applicationRepository->getApplication($applicationId);
    }

    /**
     * @throws ApplicationMetadataMismatchException|ApplicationIdentityMismatchException
     */
    private function validateIdentityAndApplicationMetadata(Identity $identity, ApplicationMetadata $applicationMetadata, Application $application): void
    {
        if ($application->identity->type !== $identity->type || $application->identity->identifier !== $identity->identifier) {
            throw new ApplicationIdentityMismatchException(sprintf('Identity mismatch for application with identifier "%s"', $application->id));
        }

        if ($application->form_id !== $applicationMetadata->formId) {
            throw new ApplicationMetadataMismatchException(sprintf('Form mismatch for application with identifier "%s', $application->id));
        }
    }

    /**
     * @throws FormNotFoundException
     */
    private function loadForm(string $formId): Form
    {
        $form = $this->formRepository->getForm($formId);
        if ($form === null) {
            throw new FormNotFoundException(sprintf('Form with identifier "%s" does not exist!', $formId));
        }
        return $form;
    }

    private function createApplication(string $id, Identity $identity, Form $form): Application
    {
        $application = $this->applicationRepository->makeApplicationForForm($form);
        $application->id = $id;
        $application->identity = $identity;
        $application->status = ApplicationStatus::Draft;
        $this->applicationRepository->saveApplication($application);
        return $application;
    }

    /**
     * @return array{Application, Form}
     * @throws Throwable
     */
    private function loadOrCreateApplicationWithForm(Identity $identity, ApplicationMetadata $applicationMetadata): array
    {
        $application = $this->loadApplicationIfExists($applicationMetadata->id);

        if ($application !== null) {
            $this->validateIdentityAndApplicationMetadata($identity, $applicationMetadata, $application);
        }

        $form = $this->loadForm($applicationMetadata->formId);

        if ($application === null) {
            $application = $this->createApplication($applicationMetadata->id, $identity, $form);
        }

        return [$application, $form];
    }

    /**
     * @throws FieldNotFoundException
     */
    private function loadField(Form $form, string $fieldCode): Field
    {
        $field = $this->formRepository->getField($form, $fieldCode);
        if ($field === null) {
            throw new FieldNotFoundException(sprintf('Field with code "%s" not found for form with identifier "%s"!', $fieldCode, $form->id));
        }
        return $field;
    }

    private function getFilePath(Application $application, Field $field): string
    {
        return sprintf('%s/%s', $application->id, $field->code);
    }

    /**
     * @throws FileNotFoundException
     */
    private function validateFileAnswer(Application $application, Field $field): void
    {
        $answer = $this->applicationRepository->getAnswer($application, $field);
        if ($answer === null) {
            throw new FileNotFoundException("Answer for file not found!");
        }

        $path = $this->getFilePath($application, $field);
        if (!$this->filesystemManager->disk(Disk::ApplicationFiles)->exists($path)) {
            throw new FileNotFoundException("File not found!");
        }
    }

    private function createOrUpdateAnswer(Application $application, Field $field, mixed $value): void
    {
        $answer = $this->applicationRepository->makeAnswer($application, $field);
        $answer->encrypted_answer = $this->encryptionService->encryptFieldValue(json_encode($value));
        $this->applicationRepository->saveAnswer($answer);
    }

    /**
     * @throws FileNotFoundException
     */
    private function processFieldValue(Application $application, FieldValue $value): void
    {
        // answer for file already exists at this point
        if ($value->field->type !== FieldType::Upload) {
            $this->createOrUpdateAnswer($application, $value->field, $value->value);
        }
    }

    private function processFieldValues(Application $application, array $fieldValues): void
    {
        foreach ($fieldValues as $fieldValue) {
            $this->processFieldValue($application, $fieldValue);
        }
    }

    /**
     * @throws Throwable
     */
    private function validateFieldValue(Application $application, FieldValue $value): void
    {
        if ($value->field->type === FieldType::Upload) {
            $this->validateFileAnswer($application, $value->field);
        }

        // TODO: validate format of certain fields
    }

    /**
     * @throws Throwable
     */
    private function validateFieldValues(Application $application, array $fieldValues): void
    {
        foreach ($fieldValues as $fieldValue) {
            $this->validateFieldValue($application, $fieldValue);
        }
    }

    /**
     * @throws Throwable
     */
    public function processFormSubmit(FormSubmit $formSubmit): Application
    {
        $application = DB::connection(Connection::Application)->transaction(function () use ($formSubmit) {
            [$application, $form] = $this->loadOrCreateApplicationWithForm($formSubmit->identity, $formSubmit->applicationMetadata);

            $json = $formSubmit->encryptedData; // TODO: decrypt
            $values = $this->decodingService->decodeFormValues($form, $json);

            $this->validateFieldValues($application, $values);
            $this->processFieldValues($application, $values);

            $application->status = ApplicationStatus::Submitted;
            $this->applicationRepository->saveApplication($application);

            return $application;
        });

        assert($application instanceof Application);
        return $application;
    }

    /**
     * @throws Throwable
     */
    private function doProcessFileUpload(FileUpload $fileUpload): void
    {
        [$application, $form] = $this->loadOrCreateApplicationWithForm($fileUpload->identity, $fileUpload->applicationMetadata);

        $field = $this->loadField($form, $fileUpload->fieldCode);
        if ($field->type !== FieldType::Upload) {
            throw new FieldTypeMismatchException(sprintf('Field "%s" type mismatch, expected: %s, actual: %s', $field->code, FieldType::Upload->value, $field->type->value));
        }

        $decryptedContents = base64_decode($fileUpload->encryptedContents); // TODO: decrypt
        $size = strlen($decryptedContents);
        $encryptedContents = $decryptedContents; // TODO: encrypt based on application specific key

        $value = [
            'mimeType' => $fileUpload->mimeType,
            'extension' => $fileUpload->extension,
            'size' => $size
        ];

        $this->createOrUpdateAnswer(
            $application,
            $field,
            $value
        );

        $path = $this->getFilePath($application, $field);
        $result = $this->filesystemManager->disk(Disk::ApplicationFiles)->put($path, $encryptedContents);
        if (!$result) {
            throw new Exception('Failed to write file to disk!');
        }
    }

    /**
     * @throws Throwable
     */
    public function processFileUpload(FileUpload $fileUpload): void
    {
        DB::connection(Connection::Application)->transaction(fn () => $this->doProcessFileUpload($fileUpload));
    }
}
