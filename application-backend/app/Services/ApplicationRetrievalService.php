<?php

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use Exception;
use MinVWS\DUSi\Application\Backend\Mappers\ApplicationMapper;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationList;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationListParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Error;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\FieldType;
use Psr\Log\LoggerInterface;
use stdClass;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
readonly class ApplicationRetrievalService
{
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

    public function getApplication(ApplicationParams $params): EncryptedResponse
    {
        try {
            $identity = $this->identityService->findIdentity($params->identity);
            if ($identity === null) {
                return $this->encryptionService->encryptCodableResponse(
                    EncryptedResponseStatus::NOT_FOUND,
                    new Error('identity_not_found', 'Identity not registered yet.'),
                    $params->publicKey
                );
            }

            $app = $this->applicationRepository->getMyApplication($identity, $params->reference);
            if ($app === null) {
                return $this->encryptionService->encryptCodableResponse(
                    EncryptedResponseStatus::NOT_FOUND,
                    new Error('application_not_found', 'Application not found.'),
                    $params->publicKey
                );
            }

            $data = null;
            if ($params->includeData) {
                $data = $this->getApplicationData($app);
            }

            $dto = $this->applicationMapper->mapApplicationToApplicationDTO($app, $data);

            return $this->encryptionService->encryptCodableResponse(
                EncryptedResponseStatus::OK,
                $dto,
                $params->publicKey
            );
        } catch (Exception $e) {
            echo $e->getMessage() . "\n";
            echo $e->getTraceAsString();
            $this->logger->error(
                'Error retrieving application: ' . $e->getMessage(),
                ['trace' => $e->getTraceAsString()]
            );

            return $this->encryptionService->encryptCodableResponse(
                EncryptedResponseStatus::INTERNAL_SERVER_ERROR,
                new Error('internal_error', 'Internal error.'),
                $params->publicKey
            );
        }
    }
}
