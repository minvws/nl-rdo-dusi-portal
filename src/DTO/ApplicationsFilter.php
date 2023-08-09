<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\DTO;

class ApplicationsFilter
{
    public array $validatedData;

    public function __construct(array $validatedData)
    {
        $this->validatedData = $validatedData;
    }
}
