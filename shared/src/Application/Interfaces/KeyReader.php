<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Interfaces;

interface KeyReader
{
    public function getKey(): string;
}
