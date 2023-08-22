<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Bridge\Tests\Client;

use MinVWS\DUSi\Shared\Bridge\Client\Client;
use MinVWS\DUSi\Shared\Bridge\Client\Exceptions\TimeoutException;
use MinVWS\DUSi\Shared\Bridge\Server\Server;
use MinVWS\DUSi\Shared\Bridge\Tests\EchoParams;
use MinVWS\DUSi\Shared\Bridge\Tests\EchoResult;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PHPUnit\Framework\TestCase;

use function pcntl_fork;
use function posix_kill;

class ClientTest extends TestCase
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
        $conn = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
        $server = new Server($conn);

        $conn->channel()->queue_purge('rpc_queue');

        $server->bind('echo', EchoParams::class, static function (EchoParams $params) {
            return new EchoResult(str_repeat($params->message, $params->times));
        });

        $server->run();
        exit(1);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->assertTrue(posix_kill($this->serverPid, SIGKILL));
    }

    public function testIfAMethodCanBeCalled(): void
    {
        parent::setUp();

        $conn = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
        $client = new Client($conn);
        $result = $client->call('echo', new EchoParams('ha', 3), EchoResult::class, timeout: 1);
        $this->assertEquals('hahaha', $result->message);
    }

    public function testIfANonExistentMethodResultsInATimeout(): void
    {
        $conn = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
        $client = new Client($conn);
        $this->expectException(TimeoutException::class);
        $client->call('methodDoesNotExist', new EchoParams('ha', 3), EchoResult::class, timeout: 1);
    }
}
