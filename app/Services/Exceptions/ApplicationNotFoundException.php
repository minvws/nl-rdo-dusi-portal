<?php
declare(strict_types=1);

namespace App\Services\Exceptions;

use Exception;

class ApplicationNotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct('Application not found!');
    }
}
