<?php // phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\DTO;

readonly class ApplicationStageAnswer
{
    public function __construct(
        public string $answerKey,
        public mixed $answerData,
    ) {
    }
}
