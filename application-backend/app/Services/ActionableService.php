<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\Services;

use MinVWS\DUSi\Shared\Serialisation\Models\Application\ActionableCounts;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ActionableCountsParams;

class ActionableService
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getActionableCounts(ActionableCountsParams $params): ActionableCounts
    {
        // TODO: messageCount is the number of messages marked as new
        // TODO: applicationCount is the number of applications that have a status REQUEST_FOR_CHANGES
        return new ActionableCounts(0, 0);
    }
}
