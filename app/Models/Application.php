<?php

declare(strict_types=1);

namespace App\Models;

use App\Shared\Models\Application\ApplicationMetadata;

/**
 * @property-read string $id
 * @property-read string $formId
 */
interface Application
{
    public function getMetadata(): ApplicationMetadata;
}
