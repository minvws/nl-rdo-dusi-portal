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
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\RPCMethods;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\SurePayAccountCheckParams;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SurePayService
{
    public function __construct(
        private readonly Client $bridgeClient
    ) {
    }

    public function accountCheck(SurePayAccountCheckParams $params): EncryptedResponse
    {
        return $this->bridgeClient->call(RPCMethods::SUREPAY_ACCOUNT_CHECK, $params, EncryptedResponse::class);
    }
}
