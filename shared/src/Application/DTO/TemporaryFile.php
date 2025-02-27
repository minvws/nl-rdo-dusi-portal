<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\DTO;

use Illuminate\Http\UploadedFile;

class TemporaryFile
{
    /**
     * @var resource
     */
    private $fileResource;
    private string $path;

    public function __construct(string $data)
    {
        $resource = tmpfile();
        if (!is_resource($resource)) {
            throw new \RuntimeException('Could not create temporary file');
        }

        $this->fileResource = $resource;

        $uri = stream_get_meta_data($this->fileResource)['uri'] ?? null;
        if (!is_string($uri)) {
            throw new \RuntimeException('Could not get temporary file URI');
        }

        $this->path = $uri;

        fwrite($this->fileResource, $data);
        fseek($this->fileResource, 0);
    }

    public function close(): void
    {
        $this->__destruct();
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getUploadedFile(): UploadedFile
    {
        return new UploadedFile(
            path: $this->path,
            originalName: '',
            test: true, // needs true because this file is not uploaded via HTTP
        );
    }

    public function __destruct()
    {
        if (is_resource($this->fileResource)) {
            fclose($this->fileResource);
        }
    }
}
