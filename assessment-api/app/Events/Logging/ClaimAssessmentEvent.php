<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Events\Logging;

class ClaimAssessmentEvent extends AssessmentApiLogEvent
{
    public const EVENT_CODE = '2011';
    public const EVENT_KEY = 'claim_assessment';
    public function __construct()
    {
        parent::__construct();
        $this->actionCode = self::AC_UPDATE;
    }
}
