<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Tests\Feature\Http\Controllers;

use MinVWS\DUSi\Application\API\Http\Helpers\ClientPublicKeyHelper;
use MinVWS\DUSi\Application\API\Models\PortalUser;
use MinVWS\DUSi\Application\API\Services\MessageService;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use Illuminate\Foundation\Testing\WithFaker;
use MinVWS\DUSi\Application\API\Tests\TestCase;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageDownloadFormat;
use Mockery;
use Mockery\MockInterface;
use Ramsey\Uuid\Uuid;

/**
 * @group message
 * @group message-controller
 */
class MessageControllerDownloadActionTest extends TestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->be(new PortalUser('123456789', '', null));
    }

    public function testMessageDownloadRequiresClientPublicKey(): void
    {
        $params = ['id' => Uuid::uuid4(), 'format' => MessageDownloadFormat::PDF->value];
        $response = $this->getJson(route('api.message-download', $params));
        $this->assertEquals(400, $response->status());
        $this->assertEquals('Missing ' . ClientPublicKeyHelper::HEADER_NAME . ' header', $response->json('message'));
    }

    public function testMessageDownloadRequiresValidClientPublicKey(): void
    {
        $params = ['id' => Uuid::uuid4(), 'format' => MessageDownloadFormat::PDF->value];
        $headers = [ClientPublicKeyHelper::HEADER_NAME => random_bytes(100)];
        $response = $this->getJson(route('api.message-download', $params), $headers);
        $this->assertEquals(400, $response->status());
        $this->assertEquals(
            'Invalid ' . ClientPublicKeyHelper::HEADER_NAME . ' header, make sure it is base64 encoded',
            $response->json('message')
        );
    }

    public function testMessageDownload(): void
    {
        $data = random_bytes(1000);

        $this->instance(
            MessageService::class,
            Mockery::mock(MessageService::class, function (MockInterface $mock) use ($data) {
                $mock->shouldReceive('getMessageDownload')->once()->andReturn(
                    new EncryptedResponse(EncryptedResponseStatus::OK, $data)
                );
            })
        );

        $params = ['id' => Uuid::uuid4(), 'format' => MessageDownloadFormat::PDF->value];
        $headers = [ClientPublicKeyHelper::HEADER_NAME => base64_encode(random_bytes(100))];
        $response = $this->get(route('api.message-download', $params), $headers);
        $this->assertEquals(200, $response->status());
        $this->assertEquals($data, $response->getContent());
    }
}
