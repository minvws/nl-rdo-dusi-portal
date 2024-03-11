<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Exceptions;

use Exception;

class SubsidyNotFoundException extends Exception
{
    public function __construct() {
        parent::__construct('Subsidy not found');
    }
}
