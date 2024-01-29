<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Tests\Feature\Http\Controllers;

use MinVWS\DUSi\Application\API\Http\Helpers\ClientPublicKeyHelper;
use MinVWS\DUSi\Application\API\Models\PortalUser;
use MinVWS\DUSi\Application\API\Services\ActionableService;
use Illuminate\Foundation\Testing\WithFaker;
use MinVWS\DUSi\Application\API\Services\Oidc\OidcUserLoa;
use MinVWS\DUSi\Application\API\Tests\TestCase;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponseStatus;
use Mockery;
use Mockery\MockInterface;
use Ramsey\Uuid\Uuid;

/**
 * @group actionable
 * @group actionable-controller
 */
class ActionableControllerTest extends TestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->be(new PortalUser('123456789', '', OidcUserLoa::SUBSTANTIAL));
    }

    public function testActionableCountRequiresClientPublicKey(): void
    {
        $response = $this->getJson(route('api.actionables-count'));
        $this->assertEquals(400, $response->status());
        $this->assertEquals('Missing ' . ClientPublicKeyHelper::HEADER_NAME . ' header', $response->json('message'));
    }

    public function testActionableCountRequiresValidClientPublicKey(): void
    {
        $headers = [ClientPublicKeyHelper::HEADER_NAME => random_bytes(100)];
        $response = $this->getJson(route('api.actionables-count'), $headers);
        $this->assertEquals(400, $response->status());
        $this->assertEquals(
            'Invalid ' . ClientPublicKeyHelper::HEADER_NAME . ' header, make sure it is base64 encoded',
            $response->json('message')
        );
    }

    public function testActionableCount(): void
    {
        $data = random_bytes(1000);

        $this->instance(
            ActionableService::class,
            Mockery::mock(ActionableService::class, static function (MockInterface $mock) use ($data) {
                $mock->expects('getActionableCounts')->andReturns(
                    new EncryptedResponse(EncryptedResponseStatus::OK, 'application/json', $data)
                );
            })
        );

        $headers = [ClientPublicKeyHelper::HEADER_NAME => base64_encode(random_bytes(100))];
        $response = $this->getJson(route('api.actionables-count', Uuid::uuid4()), $headers);
        $this->assertEquals(200, $response->status());
        $this->assertEquals($data, base64_decode($response->json()));
    }
}
