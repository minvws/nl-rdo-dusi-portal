<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Http\Responses;

use Illuminate\Http\Response;
use JsonException;
use MinVWS\Codable\Encoding\EncodingContext;
use MinVWS\Codable\Exceptions\CodableException;
use MinVWS\Codable\JSON\JSONEncoder;

use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;

class EncodableResponse extends Response
{
    protected int $jsonOptions;

    protected JSONEncoder $encoder;

    private mixed $data;

    /**
     * @throws CodableException
     * @throws JsonException
     */
    public function __construct(
        mixed $data = null,
        int $status = self::HTTP_OK,
        array $headers = [],
        ?EncodingContext $context = null,
        int $jsonOptions = JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES
    ) {
        parent::__construct('', $status, $headers);

        if (!$this->headers->has('Content-Type')) {
            $this->headers->set('Content-Type', 'application/json');
        }

        $this->jsonOptions = $jsonOptions;
        $this->encoder = new JSONEncoder($context);
        $this->setupContext($this->encoder->getContext());
        $this->setData($data);
    }

    /**
     * Subclasses can override this method to setup the context with extra default delegates etc.
     */
    protected function setupContext(EncodingContext $context): void
    {
        if ($context->getMode() === null) {
            $context->setMode(EncodingContext::MODE_OUTPUT);
        }
    }

    /**
     * Returns the encoding context.
     */
    public function getContext(): EncodingContext
    {
        return $this->encoder->getContext();
    }

    /**
     * @throws JsonException
     * @throws CodableException
     */
    protected function encode(mixed $data): string
    {
        return $this->encoder->encode($data, $this->jsonOptions);
    }

    /**
     * Returns the original data.
     */
    public function getData(): mixed
    {
        return $this->data;
    }

    /**
     * Update encoder context.
     *
     * Should call this *before* the data is set!
     */
    public function withContext(callable $callback): self
    {
        $callback($this->encoder->getContext());
        return $this;
    }

    public function setJsonOptions(int $options): self
    {
        $this->jsonOptions = $options;
        return $this;
    }

    /**
     * @throws CodableException
     * @throws JsonException
     */
    public function setData(mixed $data): self
    {
        $this->data = $data;
        $this->setContent($this->encode($data));
        return $this;
    }
}
