<?php // phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

readonly class FileUpload2
{
    public function __construct(
        public EncryptedIdentity $identity,
        public string $applicationReference,
        public string $fieldCode,
        public ?string $mimeType,
        public ?string $originalName,
        public string $data
    ) {
    }
}
