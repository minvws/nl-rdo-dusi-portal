<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Tests\Feature\Http\Controllers;

use MinVWS\DUSi\Application\API\Http\Helpers\ClientPublicKeyHelper;
use MinVWS\DUSi\Application\API\Models\PortalUser;
use MinVWS\DUSi\Application\API\Services\MessageService;
use Illuminate\Foundation\Testing\WithFaker;
use MinVWS\DUSi\Application\API\Services\Oidc\OidcUserLoa;
use MinVWS\DUSi\Application\API\Tests\TestCase;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
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
        $this->be(new PortalUser('123456789', '', OidcUserLoa::SUBSTANTIAL));
    }

    public function testMessageViewRequiresClientPublicKey(): void
    {
        $response = $this->getJson(route('api.message-view', Uuid::uuid4()));
        $this->assertEquals(400, $response->status());
        $this->assertEquals('Missing ' . ClientPublicKeyHelper::HEADER_NAME . ' header', $response->json('message'));
    }

    public function testMessageViewRequiresValidClientPublicKey(): void
    {
        $headers = [ClientPublicKeyHelper::HEADER_NAME => random_bytes(100)];
        $response = $this->getJson(route('api.message-view', Uuid::uuid4()), $headers);
        $this->assertEquals(400, $response->status());
        $this->assertEquals(
            'Invalid ' . ClientPublicKeyHelper::HEADER_NAME . ' header, make sure it is base64 encoded',
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
                    new EncryptedResponse(EncryptedResponseStatus::OK, '', '', $data)
                );
            })
        );

        $headers = [ClientPublicKeyHelper::HEADER_NAME => base64_encode(random_bytes(100))];
        $response = $this->getJson(route('api.message-view', Uuid::uuid4()), $headers);
        $this->assertEquals(200, $response->status());
        $this->assertEquals($data, base64_decode($response->json('data')));
    }
}
