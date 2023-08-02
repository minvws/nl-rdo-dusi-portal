<?php

declare(strict_types=1);

namespace App\Models;

class SubsidyStageData
{
    public function __construct(public string $id, public string $json)
    {
    }
}
