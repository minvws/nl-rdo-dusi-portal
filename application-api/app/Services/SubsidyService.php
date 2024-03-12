<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Services;

use MinVWS\DUSi\Application\API\Helpers\CacheKeyHelper;
use MinVWS\DUSi\Application\API\Repositories\CacheRepository;
use MinVWS\DUSi\Shared\Bridge\Client\Client;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\RPCMethods;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\SubsidyConceptsParams;

class SubsidyService
{
    public function __construct(
        private readonly CacheRepository $cacheRepository,
        private readonly CacheKeyHelper $cacheKeyHelper,
        private readonly Client $bridgeClient
    ) {
    }

    public function getActiveSubsidies(): string
    {
        $subsidies = $this->cacheRepository->get($this->cacheKeyHelper->keyForActiveSubsidies());

        if ($subsidies === null) {
            $json = json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            if (is_string($json) === false) {
                throw new \Exception('Could not encode empty array to JSON');
            }
            return $json;
        }

        return $subsidies;
    }

    public function getSubsidyConcepts(SubsidyConceptsParams $params): EncryptedResponse
    {
        return $this->bridgeClient->call(RPCMethods::GET_SUBSIDY_CONCEPTS, $params, EncryptedResponse::class);
    }
}
