<?php // phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

readonly class FormSubmit
{
    public function __construct(
        public EncryptedIdentity $identity,
        public ApplicationMetadata $applicationMetadata,
        public string $encryptedData
    ) {
    }
}
