<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Bridge\Shared\DTO;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Decoding\Decodable;
use MinVWS\Codable\Decoding\DecodingContainer;
use MinVWS\Codable\Encoding\EncodingContainer;
use MinVWS\Codable\Exceptions\CodablePathException;

/**
 * @template TData of Codable
 */
class MethodResult implements Codable
{
    public const DATA_CLASS = self::class . '::DATA_CLASS';

    /**
     * @param TData $data
     */
    final public function __construct(
        public readonly Codable $data
    ) {
    }

    /**
     * @SuppressWarnings(UnusedFormalParameter)
     */
    public static function decode(DecodingContainer $container, ?Decodable $object = null): static
    {
        $dataClass = $container->getContext()->getValue(self::DATA_CLASS);
        if ($dataClass === null) {
            throw new CodablePathException($container->getPath(), 'No value found in context for ' . self::DATA_CLASS);
        }

        $data = $container->{'data'}->decodeObject($dataClass);

        return new static($data);
    }

    public function encode(EncodingContainer $container): void
    {
        $container->{'data'} = $this->data;
    }
}
