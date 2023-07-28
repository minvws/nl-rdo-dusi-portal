<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Shared\Models\Application;

readonly class FileUpload
{
    public function __construct(
        public Identity $identity,
        public ApplicationMetadata $applicationMetadata,
        public string $fieldCode,
        public string $id,
        public string $mimeType,
        public ?string $extension,
        public string $encryptedContents
    ) {
    }
}
