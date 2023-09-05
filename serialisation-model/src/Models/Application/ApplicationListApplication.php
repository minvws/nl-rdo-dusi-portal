<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

use DateTimeInterface;
use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Decoding\Decodable;
use MinVWS\Codable\Decoding\DecodingContainer;
use MinVWS\Codable\Encoding\EncodingContainer;
use MinVWS\Codable\Exceptions\CodablePathException;

class ApplicationListApplication implements Codable
{
    final public function __construct(
        public readonly string $id,
        public readonly Subsidy $subsidy,
        public readonly DateTimeInterface $submittedAt,
        public readonly ?DateTimeInterface $deadlineForResponseAt,
        public readonly ApplicationStatus $status
    ) {
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     * @psalm-suppress NoValue
     * @inheritDoc
     */
    public static function decode(DecodingContainer $container, ?Decodable $object = null): static
    {
        $id = $container->{'id'}->decodeString();
        $subsidy = $container->{'subsidy'}->decodeObject(Subsidy::class);
        $submittedAt = $container->{'submittedAt'}->decodeDateTime();
        $deadlineForResponseAt = $container->{'deadlineForResponseAt'}->decodeDateTimeIfExists();
        $status = ApplicationStatus::tryFrom($container->{'status'}->decodeString());
        if ($status === null) {
            throw new CodablePathException(
                $container->getPath(),
                "Invalid status at path " . CodablePathException::convertPathToString($container->getPath())
            );
        }
        return new static($id, $subsidy, $submittedAt, $deadlineForResponseAt, $status);
    }

    public function encode(EncodingContainer $container): void
    {
        $container->{'id'} = $this->id;
        $container->{'subsidy'} = $this->subsidy;
        $container->{'submittedAt'} = $this->submittedAt;
        $container->{'deadlineForResponseAt'} = $this->deadlineForResponseAt;
        $container->{'status'} = $this->status->value;
    }
}
