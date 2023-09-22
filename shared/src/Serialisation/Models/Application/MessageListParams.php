<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

use DateTimeInterface;
use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Coding\CodableSupport;
use MinVWS\Codable\Reflection\Attributes\CodableArray;
use MinVWS\Codable\Reflection\Attributes\CodableDateTime;

/**
 * @todo Add more params
 */
class MessageListParams implements Codable
{
    use CodableSupport;

    private const DATE_FORMAT = 'Y-m-d';

    /**
     * @param array<string>|null $subsidies
     * @param array<string>|null $statuses
     */
    final public function __construct(
        public readonly EncryptedIdentity $identity,
        public readonly ClientPublicKey $publicKey,
        #[CodableDateTime(self::DATE_FORMAT)] public readonly ?DateTimeInterface $periodStart,
        #[CodableDateTime(self::DATE_FORMAT)] public readonly ?DateTimeInterface $periodEnd,
        #[CodableArray('string')] public readonly ?array $subsidies,
        #[CodableArray('string')] public readonly ?array $statuses
    ) {
    }
}
