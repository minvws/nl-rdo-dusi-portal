<?php // phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

readonly class ApplicationMetadata
{
    public function __construct(
        public string $applicationId,
        public string $subsidyStageId,
        public bool $isDraft,
    ) {
    }
}
