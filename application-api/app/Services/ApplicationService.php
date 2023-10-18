<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Services;

use Exception;
use MinVWS\DUSi\Shared\Bridge\Client\Client;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationFindOrCreateParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationListParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedApplicationFileUploadParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedApplicationSaveParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedFieldValidationParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\RPCMethods;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ApplicationService
{
    public function __construct(
        private readonly Client $bridgeClient
    ) {
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

    /**
     * @throws Exception
     */
    public function findOrCreateApplication(ApplicationFindOrCreateParams $params): EncryptedResponse
    {
        return $this->bridgeClient->call(RPCMethods::FIND_OR_CREATE_APPLICATION, $params, EncryptedResponse::class);
    }

    /**
     * @throws Exception
     */
    public function uploadApplicationFile(EncryptedApplicationFileUploadParams $params): EncryptedResponse
    {
        return $this->bridgeClient->call(RPCMethods::UPLOAD_APPLICATION_FILE, $params, EncryptedResponse::class);
    }

    /**
     * @throws Exception
     */
    public function saveApplication(EncryptedApplicationSaveParams $params): EncryptedResponse
    {
        return $this->bridgeClient->call(RPCMethods::SAVE_APPLICATION, $params, EncryptedResponse::class);
    }

    /**
     * @throws Exception
     */
    public function validateApplicationFields(EncryptedFieldValidationParams $params): EncryptedResponse
    {
        return $this->bridgeClient->call(RPCMethods::VALIDATE_FIELD, $params, EncryptedResponse::class);
    }
}
