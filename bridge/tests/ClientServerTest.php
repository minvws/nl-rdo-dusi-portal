<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Bridge\Tests;

use Exception;
use MinVWS\DUSi\Shared\Bridge\Client\Client;
use MinVWS\DUSi\Shared\Bridge\Client\Exceptions\TimeoutException;
use MinVWS\DUSi\Shared\Bridge\Server\Server;
use MinVWS\DUSi\Shared\Bridge\Shared\Connection;
use MinVWS\DUSi\Shared\Bridge\Tests\DTO\EchoParams;
use MinVWS\DUSi\Shared\Bridge\Tests\DTO\EchoResult;
use PHPUnit\Framework\TestCase;

use function pcntl_fork;
use function posix_kill;

class ClientServerTest extends TestCase
{
    private ?int $serverPid;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serverPid = pcntl_fork();

        if ($this->serverPid) {
            return; // main process
        }

        // child process, start server and wait indefinitely
        $conn = Connection::create(host: 'rabbitmq');
        $server = new Server($conn);

        $conn->connection->channel()->queue_purge('rpc_queue', true);

        $server->bind('echo', EchoParams::class, static function (EchoParams $params) {
            return new EchoResult(str_repeat($params->message, $params->times));
        });

        $server->run();
        exit(1);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        if ($this->serverPid !== null) {
            $this->assertTrue(posix_kill($this->serverPid, SIGKILL));
        }
    }

    /**
     * @throws Exception
     */
    public function testIfAMethodCanBeCalled(): void
    {
        parent::setUp();

        $conn = Connection::create(host: 'rabbitmq');
        $client = new Client($conn);
        $result = $client->call('echo', new EchoParams('ha', 3), EchoResult::class, timeout: 1);
        $this->assertEquals('hahaha', $result->message);
    }

    /**
     * @throws Exception
     */
    public function testIfANonExistentMethodResultsInATimeout(): void
    {
        $conn = Connection::create(host: 'rabbitmq');
        $client = new Client($conn);
        $this->expectException(TimeoutException::class);
        $client->call('methodDoesNotExist', new EchoParams('ha', 3), EchoResult::class, timeout: 1);
    }
}
