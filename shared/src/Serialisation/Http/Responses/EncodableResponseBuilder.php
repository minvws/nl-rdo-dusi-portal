<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Http\Responses;

use JsonException;
use MinVWS\Codable\Encoding\EncodableDelegate;
use MinVWS\Codable\Encoding\EncodingContext;
use MinVWS\Codable\Encoding\StaticEncodableDelegate;
use MinVWS\Codable\Exceptions\CodableException;
use Symfony\Component\HttpFoundation\Response;

use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;

class EncodableResponseBuilder
{
    private EncodingContext $context;
    private int $jsonOptions = JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES;

    private function __construct(
        private mixed $data,
        private int $status = Response::HTTP_OK,
        private array $headers = []
    ) {
        $this->context = new EncodingContext();
    }

    public static function create(mixed $data, int $status = 200, array $headers = []): self
    {
        return new self($data, $status, $headers);
    }

    public function status(int $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function withContext(callable $callback): self
    {
        $callback($this->context);
        return $this;
    }

    public function jsonOptions(int $options): self
    {
        $this->jsonOptions = $options;
        return $this;
    }

    /**
     * @param class-string $class
     * @param class-string<StaticEncodableDelegate>|EncodableDelegate|callable $delegate
     */
    public function registerDelegate(string $class, string|EncodableDelegate|callable $delegate): self
    {
        $this->context->registerDelegate($class, $delegate);
        return $this;
    }

    /**
     * @throws CodableException
     * @throws JsonException
     */
    public function build(): EncodableResponse
    {
        return new EncodableResponse($this->data, $this->status, $this->headers, $this->context, $this->jsonOptions);
    }
}
