<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\HsmEncryptedData;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @implements CastsAttributes<HsmEncryptedData, array|HsmEncryptedData|null>
 */
class HsmEncryptedDataCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return new HsmEncryptedData(
            data: $value['data'],
            keyLabel: $value['key_label'],
        );
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (is_null($value)) {
            return '{}';
        }

        if (is_array($value)) {
            $value = $this->get($model, $key, $value, $attributes);
        }

        if (! $value instanceof HsmEncryptedData) {
            throw new InvalidArgumentException('The given value is not an HsmEncryptedData instance.');
        }

        return json_encode([
            'data' => $value->data,
            'key_label' => $value->keyLabel,
        ]);
    }
}
