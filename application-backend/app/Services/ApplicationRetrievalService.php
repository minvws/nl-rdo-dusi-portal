<?php

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use Exception;
use MinVWS\DUSi\Application\Backend\Mappers\ApplicationMapper;
use MinVWS\DUSi\Application\Backend\Services\Traits\HandleException;
use MinVWS\DUSi\Application\Backend\Services\Traits\LoadApplication;
use MinVWS\DUSi\Application\Backend\Services\Traits\LoadIdentity;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationFile;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationList;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationListParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use Psr\Log\LoggerInterface;
use stdClass;
use Throwable;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
readonly class ApplicationRetrievalService
{
    use HandleException;
    use LoadIdentity;
    use LoadApplication;

    public function __construct(
        private EncryptionService $encryptionService,
        private ApplicationRepository $applicationRepository,
        private IdentityService $identityService,
        private ApplicationMapper $applicationMapper,
        private LoggerInterface $logger
    ) {
    }

    public function listApplications(ApplicationListParams $params): EncryptedResponse
    {
        $identity = $this->identityService->findIdentity($params->identity);
        if (empty($identity)) {
            // no identity found, so no applications (yet)
            return $this->encryptionService->encryptCodableResponse(
                EncryptedResponseStatus::OK,
                new ApplicationList([]),
                $params->publicKey
            );
        }

        $apps = $this->applicationRepository->getMyApplications($identity);
        $list = $this->applicationMapper->mapApplicationArrayToApplicationListDTO($apps);

        return $this->encryptionService->encryptCodableResponse(
            EncryptedResponseStatus::OK,
            $list,
            $params->publicKey
        );
    }

    /**
     * @throws Exception
     */
    private function getApplicationData(Application $application): object
    {
        $answers = $this->applicationRepository->getApplicationStageAnswersByStageNumber($application, 1);

        $data = new stdClass();
        foreach ($answers as $answer) {
            if ($answer->field->type === FieldType::Upload) {
                continue; // uploads are not part of the returned data
            }

            $decryptedValue = $this->encryptionService->decryptBase64EncodedData($answer->encrypted_answer);
            $decodedValue = json_decode($decryptedValue, flags: JSON_THROW_ON_ERROR);
            $data->{$answer->field->code} = $decodedValue;
        }

        return $data;
    }

    /**
     * @throws Exception
     */
    private function getApplicationFiles(Application $application): array
    {
        $files = [];

        $answers = $this->applicationRepository->getApplicationStageAnswersByStageNumber($application, 1);
        foreach ($answers as $answer) {
            if ($answer->field->type !== FieldType::Upload) {
                continue;
            }

            $decryptedValue = $this->encryptionService->decryptBase64EncodedData($answer->encrypted_answer);
            $decodedValue = json_decode($decryptedValue, flags: JSON_THROW_ON_ERROR);

            // TODO: retrieving file info should be more formalized
            // TODO: we currently fake the id and originalName, make sure we create/store these on upload!

            if (!isset($decodedValue->mimeType) || !isset($decodedValue->extension) || !isset($decodedValue->size)) {
                continue;
            }

            $files[] = new ApplicationFile(
                id: $answer->field->code,
                fieldCode: $answer->field->code,
                originalName: $answer->field->code . '.' . $decodedValue->extension,
                mimeType: $decodedValue->mimeType,
                size: $decodedValue->size
            );
        }

        return $files;
    }

    public function getApplication(ApplicationParams $params): EncryptedResponse
    {
        try {
            $identity = $this->loadIdentity($params->identity);
            $app = $this->loadApplication($identity, $params->reference);

            $data = null;
            if ($params->includeData) {
                $data = $this->getApplicationData($app);
            }

            $files = null;
            if ($params->includeFiles) {
                $files = $this->getApplicationFiles($app);
            }

            $dto = $this->applicationMapper->mapApplicationToApplicationDTO($app, $data, $files);

            return $this->encryptionService->encryptCodableResponse(
                EncryptedResponseStatus::OK,
                $dto,
                $params->publicKey
            );
        } catch (Throwable $e) {
            return $this->handleException(__METHOD__, $e, $params->publicKey);
        }
    }
}
