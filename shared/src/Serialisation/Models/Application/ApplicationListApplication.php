<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

use DateTimeInterface;
use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Coding\CodableSupport;

class ApplicationListApplication implements Codable
{
    use CodableSupport;

    public function __construct(
        public readonly string $reference,
        public readonly Subsidy $subsidy,
        public readonly DateTimeInterface $updatedAt,
        public readonly ?DateTimeInterface $expiresAt,
        public readonly ?DateTimeInterface $submittedAt,
        public readonly ?DateTimeInterface $finalReviewDeadline,
        public readonly ApplicationStatus $status,
        public readonly bool $isEditable
    ) {
    }
}
