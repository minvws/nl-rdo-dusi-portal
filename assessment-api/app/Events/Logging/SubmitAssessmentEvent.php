<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Events\Logging;

class SubmitAssessmentEvent extends AssessmentApiLogEvent
{
    public const EVENT_CODE = '2013';
    public const EVENT_KEY = 'submit_assessment';
    public function __construct()
    {
        parent::__construct();
        $this->actionCode = self::AC_UPDATE;
    }
}
