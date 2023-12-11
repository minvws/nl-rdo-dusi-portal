<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\DTO;

readonly class ApplicationStageAnswer
{
    public function __construct(
        public string $answerKey,
        public mixed $answerData,
    ) {
    }

    public function __toString(): string
    {
        return (string) $this->answerData;
    }
}
