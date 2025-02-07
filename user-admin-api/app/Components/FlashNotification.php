<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\Components;

use MinVWS\DUSi\User\Admin\API\Enums\FlashNotificationTypeEnum;

class FlashNotification
{
    public const SESSION_KEY = 'flash_notification';

    public function __construct(
        protected FlashNotificationTypeEnum $type,
        protected string $message,
    ) {
    }

    public function getType(): FlashNotificationTypeEnum
    {
        return $this->type;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
