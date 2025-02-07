<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Models;

use Illuminate\Contracts\Database\Eloquent\Castable;
use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Coding\CodableSupport;
use MinVWS\DUSi\Shared\Serialisation\Casts\CodableCast;

class FieldReference implements Codable, Castable
{
    use CodableSupport;

    public function __construct(
        public readonly int $stage,
        public readonly string $fieldCode
    ) {
    }

    /**
     * @param array $arguments
     * @return CodableCast<FieldReference>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public static function castUsing(array $arguments)
    {
        return new CodableCast(self::class);
    }
}
