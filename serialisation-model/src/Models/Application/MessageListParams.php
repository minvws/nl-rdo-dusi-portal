<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

use DateTimeInterface;
use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Decoding\Decodable;
use MinVWS\Codable\Decoding\DecodingContainer;
use MinVWS\Codable\Encoding\EncodingContainer;

/**
 * @todo Add more params
 */
class MessageListParams implements Codable
{
    private const DATE_FORMAT = 'Y-m-d';

    /**
     * @param array<string>|null $subsidies
     * @param array<string>|null $statuses
     */
    final public function __construct(
        public readonly Identity $identity,
        public readonly ?DateTimeInterface $periodStart,
        public readonly ?DateTimeInterface $periodEnd,
        public readonly ?array $subsidies,
        public readonly ?array $statuses
    ) {
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    public static function decode(DecodingContainer $container, ?Decodable $object = null): static
    {
        $identity = $container->{'identity'}->decodeObject(Identity::class);
        $periodStart = $container->{'periodStart'}->decodeDateTimeIfPresent(self::DATE_FORMAT);
        $periodEnd = $container->{'periodEnd'}->decodeDateTimeIfPresent(self::DATE_FORMAT);
        $subsidies = $container->{'subsidies'}->decodeArrayifPresent('string');
        $statuses = $container->{'statuses'}->decodeArrayifPresent('string');
        return new static($identity, $periodStart, $periodEnd, $subsidies, $statuses);
    }

    public function encode(EncodingContainer $container): void
    {
        $container->{'identity'} = $this->identity;
        $container->{'periodStart'}->encodeDateTime($this->periodStart, self::DATE_FORMAT);
        $container->{'periodEnd'}->encodeDateTime($this->periodEnd, self::DATE_FORMAT);
        $container->{'subsidies'} = $this->subsidies;
        $container->{'statuses'} = $this->statuses;
    }
}
