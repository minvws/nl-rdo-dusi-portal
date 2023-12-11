<?php

/**
 * Hsm Encrypted Data
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

use Illuminate\Contracts\Database\Eloquent\Castable;
use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Coding\CodableSupport;
use MinVWS\DUSi\Shared\Serialisation\Casts\HsmEncryptedDataCast;
use MinVWS\DUSi\Shared\Serialisation\Hsm\HsmDecryptableData;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
readonly class HsmEncryptedData implements Castable, HsmDecryptableData, Codable
{
    use CodableSupport;

    public function __construct(
        public string $data,
        public string $keyLabel,
    ) {
    }

    /**
     * Get the name of the caster class to use when casting from / to this cast target.
     *
     * @param  array<string, mixed>  $arguments
     */
    public static function castUsing(array $arguments): string
    {
        return HsmEncryptedDataCast::class;
    }

    public function getKeyLabel(): string
    {
        return $this->keyLabel;
    }

    public function getData(): string
    {
        return $this->data;
    }
}
