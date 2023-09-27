<?php // phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\DTO;

use Illuminate\Support\Collection;

/**
 * @extends Collection<array-key, ApplicationStageAnswer>
 */
class LetterStageData extends Collection
{
    public function __get($key): mixed
    {
        return $this->get($key)?->answerData;
    }
}
