<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Events\Logging;

class AssignAssessmentEvent extends AssessmentApiLogEvent
{
    public const EVENT_CODE = '2014';
    public const EVENT_KEY = 'assign_assessment';
    public function __construct()
    {
        parent::__construct();
        $this->actionCode = self::AC_UPDATE;
    }
}
