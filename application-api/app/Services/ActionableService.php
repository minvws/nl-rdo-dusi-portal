<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Services;

use Exception;
use MinVWS\DUSi\Shared\Bridge\Client\Client;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ActionableCountsParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\RPCMethods;

class ActionableService
{
    public function __construct(private readonly Client $bridgeClient)
    {
    }

    /**
     * @throws Exception
     */
    public function getActionableCounts(ActionableCountsParams $params): EncryptedResponse
    {
        return $this->bridgeClient->call(RPCMethods::GET_ACTIONABLE_COUNTS, $params, EncryptedResponse::class);
    }
}
