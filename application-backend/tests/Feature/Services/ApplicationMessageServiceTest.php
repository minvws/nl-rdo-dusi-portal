<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Tests\Feature\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use MinVWS\Codable\Coding\Codable;
use MinVWS\DUSi\Application\Backend\Services\ApplicationMessageService;
use MinVWS\DUSi\Application\Backend\Services\ResponseEncryptionService;
use MinVWS\DUSi\Application\Backend\Tests\MocksEncryptionAndHashing;
use MinVWS\DUSi\Application\Backend\Tests\TestCase;
use MinVWS\DUSi\Shared\Application\Models\ApplicationMessage;
use MinVWS\DUSi\Shared\Application\Models\Disk;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ClientPublicKey;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\HsmEncryptedData;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedIdentity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Error;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\IdentityType;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Message as MessageDTO;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageDownloadFormat;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageDownloadParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageList;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageListParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageParams;
use Queue;

/**
 * @group application
 * @group application-message-service
 */
class ApplicationMessageServiceTest extends TestCase
{
    use DatabaseTransactions;
    use MocksEncryptionAndHashing;
    use WithFaker;

    private ApplicationMessageService $applicationMessageService;
    private ResponseEncryptionService $responseEncryptionService;

    private Filesystem $filesystem;

    private Identity $identity;
    private EncryptedIdentity $encryptedIdentity;
    private EncryptedIdentity $invalidEncryptedIdentity;

    private ClientPublicKey $clientPublicKey;
    private mixed $keyPair;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();

        // Must be defined before services
        $this->filesystem = Storage::fake(Disk::APPLICATION_FILES);

        $this->applicationMessageService = App::make(ApplicationMessageService::class);
        $this->responseEncryptionService = App::make(ResponseEncryptionService::class);

        $this->keyPair = sodium_crypto_box_keypair();
        $publicKey = sodium_crypto_box_publickey($this->keyPair);
        $this->clientPublicKey = new ClientPublicKey($publicKey);

        $this->identity = Identity::factory()->create();

        $this->encryptedIdentity = new EncryptedIdentity(
            type: IdentityType::CitizenServiceNumber,
            encryptedIdentifier: new HsmEncryptedData($this->identity->hashed_identifier, ''),
        );

        $this->invalidEncryptedIdentity = new EncryptedIdentity(
            type: IdentityType::CitizenServiceNumber,
            encryptedIdentifier: new HsmEncryptedData($this->faker->uuid, ''),
        );
    }

    public function testListMessages(): void
    {
        ApplicationMessage::factory()->count(10)->forIdentity($this->identity)->create();
        $encryptedResponse = $this->getMessageListResponse($this->encryptedIdentity);

        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::OK, $encryptedResponse->status);

        $messageList = $this->getDecryptedCodableResponse($encryptedResponse, MessageList::class);
        $this->assertCount(10, $messageList->items);
    }

    public function testListMessagesWithInvalidIdentity(): void
    {
        ApplicationMessage::factory()->count(10)->forIdentity($this->identity)->create();
        $encryptedResponse = $this->getMessageListResponse($this->invalidEncryptedIdentity);

        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::OK, $encryptedResponse->status);

        $messageList = $this->getDecryptedCodableResponse($encryptedResponse, MessageList::class);
        $this->assertCount(0, $messageList->items);
    }

    public function testGetMessage(): void
    {
        $message = ApplicationMessage::factory()->forIdentity($this->identity)->withLetter()->create();
        $encryptedResponse = $this->getMessageResponse($this->encryptedIdentity, $message->id);

        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::OK, $encryptedResponse->status);

        $messageDto = $this->getDecryptedCodableResponse($encryptedResponse, MessageDTO::class);
        $this->assertEquals($message->id, $messageDto->id);
        $this->assertEquals('DUMMY HTML', $messageDto->body);
    }

    public function testGetMessageWithInvalidIdentity(): void
    {
        $message = ApplicationMessage::factory()->forIdentity($this->identity)->withLetter()->create();
        $encryptedResponse = $this->getMessageResponse($this->invalidEncryptedIdentity, $message->id);

        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::NOT_FOUND, $encryptedResponse->status);

        $error = $this->getDecryptedCodableResponse($encryptedResponse, Error::class);
        $this->assertEquals('message_not_found', $error->code);
    }

    public function testGetMessageWithInvalidApplicationMessage(): void
    {
        $encryptedResponse = $this->getMessageResponse($this->encryptedIdentity, $this->faker->uuid);

        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::NOT_FOUND, $encryptedResponse->status);

        $error = $this->getDecryptedCodableResponse($encryptedResponse, Error::class);
        $this->assertEquals('message_not_found', $error->code);
    }

    public function testGetMessageWithInvalidHtmlContent(): void
    {
        $message = ApplicationMessage::factory()->forIdentity($this->identity)->create();
        $encryptedResponse = $this->getMessageResponse($this->encryptedIdentity, $message->id);

        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::NOT_FOUND, $encryptedResponse->status);

        $error = $this->getDecryptedCodableResponse($encryptedResponse, Error::class);
        $this->assertEquals('message_not_found', $error->code);
    }

    public function testGetMessageDownloadHtml(): void
    {
        $message = ApplicationMessage::factory()->forIdentity($this->identity)->withLetter()->create();
        $encryptedResponse = $this->getMessageDownloadHtmlResponse($this->encryptedIdentity, $message->id);

        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::OK, $encryptedResponse->status);
        $this->assertEquals('text/html', $encryptedResponse->contentType);

        $messageDownload = $this->getDecryptedResponse($encryptedResponse);
        $this->assertEquals('DUMMY HTML', $messageDownload);
    }

    public function testGetMessageDownloadPdf(): void
    {
        $message = ApplicationMessage::factory()->forIdentity($this->identity)->withLetter()->create();
        $encryptedResponse = $this->getMessageDownloadPdfResponse($this->encryptedIdentity, $message->id);

        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::OK, $encryptedResponse->status);
        $this->assertEquals('application/pdf', $encryptedResponse->contentType);

        $messageDownload = $this->getDecryptedResponse($encryptedResponse);
        $this->assertEquals('DUMMY PDF', $messageDownload);
    }

    public function testGetMessageDownloadWithInvalidIdentity(): void
    {
        $message = ApplicationMessage::factory()->forIdentity($this->identity)->withLetter()->create();
        $encryptedResponse = $this->getMessageDownloadHtmlResponse($this->invalidEncryptedIdentity, $message->id);

        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::NOT_FOUND, $encryptedResponse->status);

        $error = $this->getDecryptedCodableResponse($encryptedResponse, Error::class);
        $this->assertEquals('message_not_found', $error->code);
    }

    public function testGetMessageDownloadWithInvalidApplicationMessage(): void
    {
        $encryptedResponse = $this->getMessageDownloadHtmlResponse($this->encryptedIdentity, $this->faker->uuid);

        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::NOT_FOUND, $encryptedResponse->status);

        $error = $this->getDecryptedCodableResponse($encryptedResponse, Error::class);
        $this->assertEquals('message_not_found', $error->code);
    }

    public function testGetMessageDownloadWithInvalidHtmlContent(): void
    {
        $message = ApplicationMessage::factory()->forIdentity($this->identity)->create();
        $encryptedResponse = $this->getMessageDownloadHtmlResponse($this->encryptedIdentity, $message->id);

        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::NOT_FOUND, $encryptedResponse->status);

        $error = $this->getDecryptedCodableResponse($encryptedResponse, Error::class);
        $this->assertEquals('message_not_found', $error->code);
    }

    public function testGetMessageDownloadWithInvalidPdfContent(): void
    {
        $message = ApplicationMessage::factory()->forIdentity($this->identity)->create();
        $encryptedResponse = $this->getMessageDownloadPdfResponse($this->encryptedIdentity, $message->id);

        $this->assertInstanceOf(EncryptedResponse::class, $encryptedResponse);
        $this->assertEquals(EncryptedResponseStatus::NOT_FOUND, $encryptedResponse->status);

        $error = $this->getDecryptedCodableResponse($encryptedResponse, Error::class);
        $this->assertEquals('message_not_found', $error->code);
    }

    private function getMessageResponse(EncryptedIdentity $encryptedIdentity, string $messageId): EncryptedResponse
    {
        $params = new MessageParams($encryptedIdentity, $this->clientPublicKey, $messageId);

        return $this->applicationMessageService->getMessage($params);
    }

    private function getMessageDownloadResponse(
        EncryptedIdentity $encryptedIdentity,
        string $messageId,
        MessageDownloadFormat $format
    ): EncryptedResponse {
        $params = new MessageDownloadParams($encryptedIdentity, $this->clientPublicKey, $messageId, $format);

        return $this->applicationMessageService->getMessageDownload($params);
    }

    private function getMessageDownloadHtmlResponse(
        EncryptedIdentity $encryptedIdentity,
        string $messageId
    ): EncryptedResponse {
        return $this->getMessageDownloadResponse($encryptedIdentity, $messageId, MessageDownloadFormat::HTML);
    }

    private function getMessageDownloadPdfResponse(
        EncryptedIdentity $encryptedIdentity,
        string $messageId
    ): EncryptedResponse {
        return $this->getMessageDownloadResponse($encryptedIdentity, $messageId, MessageDownloadFormat::PDF);
    }

    private function getMessageListResponse(EncryptedIdentity $encryptedIdentity): EncryptedResponse
    {
        $params = new MessageListParams($encryptedIdentity, $this->clientPublicKey, null, null, [], []);

        return $this->applicationMessageService->listMessages($params);
    }

    private function getDecryptedResponse(EncryptedResponse $encryptedResponse): mixed
    {
        return $this->responseEncryptionService->decrypt($encryptedResponse, $this->keyPair);
    }

    /**
     * @param EncryptedResponse $encryptedResponse
     * @param class-string<Codable> $class
     * @return mixed
     */
    private function getDecryptedCodableResponse(EncryptedResponse $encryptedResponse, string $class): mixed
    {
        return $this->responseEncryptionService->decryptCodable($encryptedResponse, $class, $this->keyPair);
    }
}
