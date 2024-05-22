<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Models\Submission;

use JsonSerializable;
use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Decoding\Decodable;
use MinVWS\Codable\Decoding\DecodingContainer;
use MinVWS\Codable\Encoding\EncodingContainer;

class FileList implements Codable, JsonSerializable
{
    /**
     * @param File[] $items
     */
    public function __construct(
        public readonly array $items
    ) {
    }

    public function encode(EncodingContainer $container): void
    {
        $container->encodeArray($this->items);
    }

    /**
     * @inheritDoc
     */
    public static function decode(DecodingContainer $container, ?Decodable $object = null): Decodable
    {
        return new self($container->decodeArray(File::class));
    }

    /**
     * @return string[]
     */
    public function getFileIds(): array
    {
        return array_map(fn(File $file) => $file->id, $this->items);
    }

    /**
     * @return File[]
     */
    public function jsonSerialize(): array
    {
        return $this->items;
    }

    public function __toString(): string
    {
        return (string)array_reduce(
            $this->getFileIds(),
            fn(null|string $carry, string $id) => $carry . $id
        );
    }

    public function count(): int
    {
        return count($this->items);
    }
}
