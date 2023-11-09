<?php

/**
 * Application Message Service
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use Exception;
use MinVWS\DUSi\Application\Backend\Events\Logging\ListMessagesEvent;
use MinVWS\DUSi\Application\Backend\Events\Logging\ViewMessageEvent;
use MinVWS\DUSi\Application\Backend\Mappers\ApplicationMapper;
use MinVWS\DUSi\Application\Backend\Services\Traits\LoadIdentity;
use MinVWS\DUSi\Shared\Application\Helpers\EncryptedResponseExceptionHelper;
use MinVWS\DUSi\Shared\Application\Repositories\ApplicationMessageRepository;
use MinVWS\DUSi\Shared\Application\Services\ApplicationFileManager;
use MinVWS\DUSi\Shared\Application\Services\ResponseEncryptionService;
use MinVWS\DUSi\Shared\Serialisation\Exceptions\EncryptedResponseException;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageDownloadFormat;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageDownloadParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageList;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageListParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\RPCMethods;
use MinVWS\Logging\Laravel\LogService;
use Throwable;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
readonly class ApplicationMessageService
{
    use LoadIdentity;

    public function __construct(
        private ResponseEncryptionService $responseEncryptionService,
        private ApplicationFileManager $applicationFileManager,
        private ApplicationMessageRepository $messageRepository,
        private IdentityService $identityService,
        private ApplicationMapper $applicationMapper,
        private EncryptedResponseExceptionHelper $exceptionHelper,
        private LogService $logService
    ) {
    }

    public function listMessages(MessageListParams $params): EncryptedResponse
    {
        try {
            return $this->doListMessages($params);
        } catch (Throwable $e) {
            return $this->exceptionHelper->processException(
                $e,
                __CLASS__,
                __METHOD__,
                RPCMethods::LIST_MESSAGES,
                $params->publicKey
            );
        }
    }

    private function doListMessages(MessageListParams $params): EncryptedResponse
    {
        $identity = $this->identityService->findIdentity($params->identity);
        if ($identity === null) {
            // Identity not known in system, so no applications / messages yet.
            return $this->responseEncryptionService->encryptCodable(
                EncryptedResponseStatus::OK,
                new MessageList([]),
                $params->publicKey
            );
        }

        $applicationMessages = $this->messageRepository->getMyMessages($identity);
        $list = $this->applicationMapper->mapApplicationMessageArrayToMessageListDTO($applicationMessages);

        $this->logService->log((new ListMessagesEvent())
            ->withData([
                'userId' => $identity->id,
                'type' => 'messages',
                'typeId' => 3,
            ]));

        return $this->responseEncryptionService->encryptCodable(
            EncryptedResponseStatus::OK,
            $list,
            $params->publicKey
        );
    }

    public function getMessage(MessageParams $params): EncryptedResponse
    {
        try {
            return $this->doGetMessage($params);
        } catch (Throwable $e) {
            return $this->exceptionHelper->processException(
                $e,
                __CLASS__,
                __METHOD__,
                RPCMethods::GET_MESSAGE,
                $params->publicKey
            );
        }
    }

    private function doGetMessage(MessageParams $params): EncryptedResponse
    {
        $identity = $this->loadIdentity($params->identity);

        $message = $this->messageRepository->getMyMessage($identity, $params->id);
        if ($message === null) {
            throw new EncryptedResponseException(
                EncryptedResponseStatus::NOT_FOUND,
                'message_not_found'
            );
        }

        try {
            $body = $this->applicationFileManager->readEncryptedFile($message->html_path);
        } catch (Exception $e) {
            throw new EncryptedResponseException(
                EncryptedResponseStatus::NOT_FOUND,
                'message_file_not_found'
            );
        }

        if (empty($body)) {
            throw new EncryptedResponseException(
                EncryptedResponseStatus::NOT_FOUND,
                'message_file_not_found'
            );
        }

        $dto = $this->applicationMapper->mapApplicationMessageToMessageDTO($message, $body);

        $this->logService->log((new ViewMessageEvent())
            ->withData([
                'messageId' => $message->id,
                'userId' => $identity->id,
                'type' => 'messages',
                'typeId' => 3,
            ]));


        return $this->responseEncryptionService->encryptCodable(
            EncryptedResponseStatus::OK,
            $dto,
            $params->publicKey
        );
    }

    public function getMessageDownload(MessageDownloadParams $params): EncryptedResponse
    {
        try {
            return $this->doGetMessageDownload($params);
        } catch (Throwable $e) {
            return $this->exceptionHelper->processException(
                $e,
                __CLASS__,
                __METHOD__,
                RPCMethods::GET_MESSAGE_DOWNLOAD,
                $params->publicKey
            );
        }
    }

    private function doGetMessageDownload(MessageDownloadParams $params): EncryptedResponse
    {
        $identity = $this->loadIdentity($params->identity);

        $message = $this->messageRepository->getMyMessage($identity, $params->id);
        if ($message === null) {
            throw new EncryptedResponseException(
                EncryptedResponseStatus::NOT_FOUND,
                'message_not_found'
            );
        }

        try {
            $content = match ($params->format) {
                MessageDownloadFormat::HTML => $this->applicationFileManager->readEncryptedFile($message->html_path),
                MessageDownloadFormat::PDF => $this->applicationFileManager->readEncryptedFile($message->pdf_path),
            };
        } catch (Exception $e) {
            throw new EncryptedResponseException(
                EncryptedResponseStatus::NOT_FOUND,
                'message_file_not_found'
            );
        }

        if (empty($content)) {
            throw new EncryptedResponseException(
                EncryptedResponseStatus::NOT_FOUND,
                'message_file_not_found'
            );
        }

        $contentType =  match ($params->format) {
            MessageDownloadFormat::HTML => 'text/html',
            MessageDownloadFormat::PDF => 'application/pdf'
        };

        return $this->responseEncryptionService->encrypt(
            EncryptedResponseStatus::OK,
            $content,
            $contentType,
            $params->publicKey
        );
    }
}
