<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Services;

use Exception;
use MinVWS\DUSi\Shared\Bridge\Client\Client;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationFileParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\RPCMethods;

class ApplicationFileService
{
    public function __construct(
        private readonly Client $bridgeClient
    ) {
    }

    /**
     * @throws Exception
     */
    public function getApplicationFile(ApplicationFileParams $params): EncryptedResponse
    {
        return $this->bridgeClient->call(RPCMethods::GET_APPLICATION_FILE, $params, EncryptedResponse::class);
    }

    public function deleteApplicationFile(ApplicationFileParams $params): EncryptedResponse
    {
        return $this->bridgeClient->call(RPCMethods::DELETE_APPLICATION_FILE, $params, EncryptedResponse::class);
    }
}
