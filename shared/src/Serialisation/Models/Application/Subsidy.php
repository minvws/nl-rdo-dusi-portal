<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

use DateTimeInterface;
use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Coding\CodableSupport;

class Subsidy implements Codable
{
    use CodableSupport;

    public function __construct(
        public readonly string $code,
        public readonly string $title,
        public readonly string $description,
        public readonly ?string $pageUrl,
        public readonly DateTimeInterface $validFrom,
        public readonly ?DateTimeInterface $validTo,
        public readonly bool $isOpenForNewApplications,
        public readonly bool $allowMultipleApplications,
    ) {
    }
}
