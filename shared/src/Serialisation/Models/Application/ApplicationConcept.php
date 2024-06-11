<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

use DateTimeInterface;
use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Coding\CodableSupport;

/**
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class ApplicationConcept implements Codable
{
    use CodableSupport;

    public function __construct(
        public readonly string $reference,
        public readonly string $subsidyCode,
        public readonly DateTimeInterface $createdAt,
        public readonly DateTimeInterface $updatedAt,
        public readonly ?DateTimeInterface $expiresAt,
        public readonly ApplicationStatus $status,
    ) {
    }
}
