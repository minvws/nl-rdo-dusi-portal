<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Coding\CodableSupport;

class SubsidyOverview implements Codable
{
    use CodableSupport;

    /**
     * @param ApplicationListApplication[] $applications
     */
    public function __construct(
        public readonly Subsidy $subsidy,
        public readonly bool $newConceptAllowed,
        public readonly bool $hasApprovedApplication,
        public readonly bool $hasRejectedApplication,
        public readonly array $applications,
    ) {
    }
}
