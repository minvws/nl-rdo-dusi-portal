<?php // phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MinVWS\DUSi\Assessment\API\DTO\DispositionMailData;

readonly class LetterGeneratedEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public DispositionMailData $dispositionData)
    {
    }
}
