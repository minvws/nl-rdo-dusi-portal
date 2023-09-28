<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\Enums;

enum PaymentPreValidation: string
{
    // is returned when the account identification was successfully validated to an account that can receive
    case Pass = 'PASS';
    // is returned if the payment will definitely fail.
    case WillFail = 'WILL_FAIL';
    // is returned in case the account identification was not successfully validated to an account that can
    // receive funds, however, the responding bank is unable to provide a definitive answer.
    case Warning = 'WARNING';
}
