<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services\Clamav;

use Exception;
use Socket\Raw\Factory;
use Socket\Raw\Socket;
use Xenolope\Quahog\Client;

class ClamAvService
{
    public function __construct(
        protected bool $enabled = false,
        protected string $preferredSocket = 'unix_socket',
        protected string $unixSocket = 'unix:///var/run/clamav/clamd.ctl',
        protected string $tcpSocket = 'tcp://127.0.0.1:3310',
        protected ?int $socketConnectTimeout = null,
        protected int $socketReadTimeout = 30,
    ) {
    }

    /**
     * @throws Exception
     */
    public function getSocket(): Socket
    {
        $socket = match ($this->preferredSocket) {
            'unix_socket' => $this->unixSocket,
            'tcp_socket' => $this->tcpSocket,
            default => throw new Exception('Invalid socket type'),
        };

        return (new Factory())->createClient($socket, $this->socketConnectTimeout);
    }

    /**
     * @throws Exception
     */
    public function getClamAvClient(?Socket $socket = null): Client
    {
        $socket ??= $this->getSocket();

        return new Client($socket, $this->socketReadTimeout, PHP_NORMAL_READ);
    }

    public function enabled(): bool
    {
        return $this->enabled;
    }
}
