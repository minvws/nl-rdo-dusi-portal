<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Coding\CodableSupport;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\Subsidy;

class SubsidyConcepts implements Codable
{
    use CodableSupport;

    public function __construct(
        public readonly Subsidy $subsidy,
        public readonly array $concepts,
    ) {
    }
}
