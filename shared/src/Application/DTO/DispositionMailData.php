<?php // phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\DTO;

readonly class DispositionMailData
{
    public function __construct(
        public string $toName,
        public string $toAddress,
    ) {
    }
}
