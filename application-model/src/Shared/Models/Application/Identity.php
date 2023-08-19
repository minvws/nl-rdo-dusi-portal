<?php // phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Shared\Models\Application;

readonly class Identity
{
    public function __construct(
        public IdentityType $type,
        public string $identifier
    ) {
    }
}
