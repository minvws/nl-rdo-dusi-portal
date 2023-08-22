<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Bridge\DTO;

use Closure;

class Binding
{
    public function __construct(
        public readonly string $method,
        public readonly ?string $paramsClass,
        public readonly Closure $callback
    ) {
    }
}
