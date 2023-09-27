<?php // phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MinVWS\DUSi\Shared\Application\DTO\DispositionMailData;

readonly class LetterGeneratedEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public DispositionMailData $dispositionData)
    {
    }
}
