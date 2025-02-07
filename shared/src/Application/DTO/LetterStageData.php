<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\DTO;

use DateTime;
use Illuminate\Support\Collection;

/**
 * @extends Collection<array-key, ApplicationStageAnswer>
 */
class LetterStageData extends Collection
{
    public ?DateTime $createdAt;
    public ?DateTime $submittedAt;
    public ?DateTime $closedAt;

    public function __get($key): mixed
    {
        return $this->get($key)?->answerData;
    }

    public function __isset(string $key): bool
    {
        return $this->has($key);
    }
}
