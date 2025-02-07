<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Events\Logging;

use MinVWS\Logging\Laravel\Events\Logging\GeneralLogEvent;

class ExportApplicationsEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '2016';
    public const EVENT_KEY = 'export_applications';
    public function __construct()
    {
        parent::__construct();
        $this->actionCode = self::AC_EXECUTE;
    }
}
