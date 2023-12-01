<?php // phpcs:disable PSR1.Files.SideEffects

/**
 * Cast for codables.
 */

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\JSON\JSONDecoder;
use MinVWS\Codable\JSON\JSONEncoder;

/**
 * @template T of Codable
 * @implements CastsAttributes<T|null, T|null>
 */
readonly class CodableCast implements CastsAttributes
{
    private JSONDecoder $decoder;
    private JSONEncoder $encoder;

    /**
     * @param class-string<T> $class
     */
    public function __construct(private string $class)
    {
        $this->decoder = new JSONDecoder();
        $this->encoder = new JSONEncoder();
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return T|null
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Codable
    {
        return $value === null ? null : $this->decoder->decode($value)->decodeObject($this->class);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param T|null $value
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        return [$key => $value == null ? null : $this->encoder->encode($value)];
    }
}
