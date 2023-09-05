<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Tests\Feature\Http\Controllers;

use MinVWS\DUSi\Application\API\Http\Middleware\RequireClientPublicKey;
use MinVWS\DUSi\Application\API\Models\PortalUser;
use MinVWS\DUSi\Application\API\Services\MessageService;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedPayload;
use Illuminate\Foundation\Testing\WithFaker;
use MinVWS\DUSi\Application\API\Tests\TestCase;
use Mockery;
use Mockery\MockInterface;
use Ramsey\Uuid\Uuid;

/**
 * @group message
 * @group message-controller
 */
class MessageControllerViewActionTest extends TestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->be(new PortalUser('123456789', '', ''));
    }

    public function testMessageViewRequiresClientPublicKey(): void
    {
        $response = $this->getJson(route('api.message-view', Uuid::uuid4()));
        $this->assertEquals(400, $response->status());
        $this->assertEquals('Missing ' . RequireClientPublicKey::HEADER_NAME . ' header', $response->json('message'));
    }

    public function testMessageViewRequiresValidClientPublicKey(): void
    {
        $headers = [RequireClientPublicKey::HEADER_NAME => random_bytes(100)];
        $response = $this->getJson(route('api.message-view', Uuid::uuid4()), $headers);
        $this->assertEquals(400, $response->status());
        $this->assertEquals(
            'Invalid ' . RequireClientPublicKey::HEADER_NAME . ' header, make sure it is base64 encoded',
            $response->json('message')
        );
    }

    public function testMessageView(): void
    {
        $data = random_bytes(1000);

        $this->instance(
            MessageService::class,
            Mockery::mock(MessageService::class, function (MockInterface $mock) use ($data) {
                $mock->shouldReceive('getMessage')->once()->andReturn(
                    new EncryptedPayload($data)
                );
            })
        );

        $headers = [RequireClientPublicKey::HEADER_NAME => base64_encode(random_bytes(100))];
        $response = $this->getJson(route('api.message-view', Uuid::uuid4()), $headers);
        $this->assertEquals(200, $response->status());
        $content = base64_decode($response->json(), true);
        $this->assertEquals($data, $content);
    }
}
