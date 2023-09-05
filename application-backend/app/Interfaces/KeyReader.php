<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Interfaces;

interface KeyReader
{
    public function getKey(): string;
}
