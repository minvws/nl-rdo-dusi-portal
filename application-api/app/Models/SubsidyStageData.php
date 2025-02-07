<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Models;

class SubsidyStageData
{
    public function __construct(public string $id, public string $json)
    {
    }
}
