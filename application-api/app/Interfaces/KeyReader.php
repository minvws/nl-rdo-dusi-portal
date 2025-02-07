<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Interfaces;

interface KeyReader
{
    public function getKey(): string;
}
