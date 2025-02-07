<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Services\Exceptions;

use Exception;

class SubsidyStageNotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct('Form not found!');
    }
}
