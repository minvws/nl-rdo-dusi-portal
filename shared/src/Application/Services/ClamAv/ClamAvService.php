<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Services\ClamAv;

use Exception;
use Socket\Raw\Factory;
use Socket\Raw\Socket;
use Xenolope\Quahog\Client as ClamAvClient;
use Xenolope\Quahog\Result as ClamAvScanResult;

class ClamAvService
{
    protected ?ClamAvClient $clamAvClient = null;

    public function __construct(
        protected bool $enabled = false,
        protected string $preferredSocket = 'unix_socket',
        protected string $unixSocket = 'unix:///var/run/clamav/clamd.ctl',
        protected string $tcpSocket = 'tcp://127.0.0.1:3310',
        protected ?int $socketConnectTimeout = null,
        protected int $socketReadTimeout = 30,
        protected ?string $setFilePermissionsBeforeScan = null,
    ) {
    }

    public function enabled(): bool
    {
        return $this->enabled;
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
    public function getClamAvClient(?Socket $socket = null): ClamAvClient
    {
        if ($this->clamAvClient) {
            return $this->clamAvClient;
        }

        $socket ??= $this->getSocket();

        $this->clamAvClient = new ClamAvClient($socket, $this->socketReadTimeout, PHP_NORMAL_READ);
        return $this->clamAvClient;
    }

    /**
     * Scans a file with ClamAV.
     *
     * The file permissions will be set before scanning if the setFilePermissionsBeforeScan property is set.
     *
     * @throws Exception
     */
    public function scanFile(string $path): ClamAvScanResult
    {
        $this->setFilePermissions($path);

        return $this->getClamAvClient()->scanFile($path);
    }

    /**
     * Will run chmod for the specified path with the configured file permissions.
     * The expected file permissions should be in octal format (ie: 0640)
     *
     * This is useful when the clamav service is running as a different user than the web server.
     *
     * @param string $path
     * @return void
     */
    protected function setFilePermissions(string $path): void
    {
        if (!$this->setFilePermissionsBeforeScan) {
            return;
        }

        chmod($path, (int) octdec($this->setFilePermissionsBeforeScan));
    }
}
