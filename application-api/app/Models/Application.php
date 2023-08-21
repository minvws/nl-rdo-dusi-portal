<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Models;

use MinVWS\DUSi\Shared\Application\Shared\Models\Application\ApplicationMetadata;

/**
 * @property-read string $id
 * @property-read string $formId
 */
interface Application
{
    public function getMetadata(): ApplicationMetadata;
}
