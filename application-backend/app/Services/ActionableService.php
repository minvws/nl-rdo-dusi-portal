<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use MinVWS\DUSi\Application\Backend\Services\Traits\LoadIdentity;
use MinVWS\DUSi\Shared\Application\Helpers\EncryptedResponseExceptionHelper;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationMessageRepository;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationRepository;
use MinVWS\DUSi\Shared\Application\Services\ResponseEncryptionService;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ActionableCounts;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ActionableCountsParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\RPCMethods;
use Throwable;

class ActionableService
{
    use LoadIdentity;

    public function __construct(
        private readonly ResponseEncryptionService $responseEncryptionService,
        private readonly ApplicationMessageRepository $messageRepository,
        private readonly ApplicationRepository $applicationRepository,
        private readonly IdentityService $identityService,
        private readonly EncryptedResponseExceptionHelper $exceptionHelper,
    ) {
    }

    public function getActionableCounts(ActionableCountsParams $params): EncryptedResponse
    {
        try {
            return $this->doGetActionableCounts($params);
        } catch (Throwable $e) {
            return $this->exceptionHelper->processException(
                $e,
                __CLASS__,
                __METHOD__,
                RPCMethods::GET_ACTIONABLE_COUNTS,
                $params->publicKey
            );
        }
    }

    private function doGetActionableCounts(ActionableCountsParams $params): EncryptedResponse
    {
        $identity = $this->identityService->findIdentity($params->identity);

        if ($identity === null) {
            // Identity not known in system, so no applications / messages yet.
            return $this->responseEncryptionService->encryptCodable(
                EncryptedResponseStatus::OK,
                new ActionableCounts(0, 0),
                $params->publicKey
            );
        }

        $unreadMessagesCount = $this->messageRepository->getMyUnreadMessagesCount($identity);
        $applicationCount = $this->applicationRepository->getMyApplicationsThatNeededChangesCount($identity);

        $count = new ActionableCounts($unreadMessagesCount, $applicationCount);

        return $this->responseEncryptionService->encryptCodable(
            EncryptedResponseStatus::OK,
            $count,
            $params->publicKey
        );
    }
}
