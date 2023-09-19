<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Models\DTO;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Database\Eloquent\Model;
use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Coding\CodableSupport;
use MinVWS\Codable\Decoding\Decodable;
use MinVWS\Codable\Decoding\DecodingContainer;
use MinVWS\Codable\Encoding\EncodingContainer;
use MinVWS\Codable\Exceptions\CodableException;
use MinVWS\Codable\JSON\JSONDecoder;
use MinVWS\Codable\JSON\JSONEncoder;
use MinVWS\DUSi\Shared\Application\Models\Answer;

abstract readonly class Condition implements Codable, Castable
{
    use CodableSupport {
        decode as baseDecode;
        encode as baseEncode;
    }

    protected static function getType(): string
    {
        if (
            !str_starts_with(static::class, __NAMESPACE__) ||
            !str_ends_with(static::class, 'Condition')
        ) {
            throw new CodableException('Incompatible condition subclass ' . static::class);
        }

        return lcfirst(
            substr(
                static::class,
                strlen(__NAMESPACE__) + 1,
                -strlen('Condition')
            )
        );
    }

    /**
     * @return class-string<Condition>
     */
    private static function getClassForType(string $type): string
    {
        $class = __NAMESPACE__ . '\\' . ucfirst($type) . 'Condition';

        if (!class_exists($class) || !is_a($class, self::class, true)) {
            throw new CodableException('Incompatible condition type "' . $type . '"');
        }

        return $class;
    }

    public function encode(EncodingContainer $container): void
    {
        $container->{'type'} = static::getType();
        $this->baseEncode($container);
    }

    public static function decode(DecodingContainer $container, ?Decodable $object = null): Decodable
    {
        if (static::class !== self::class) {
            return static::baseDecode($container, $object);
        }

        $type = $container->{'type'}->decodeString();
        /** @var class-string<Condition> $class */
        $class = self::getClassForType($type);
        return $class::decode($container, $object);
    }

    public static function castUsing(array $arguments): CastsAttributes
    {
        return new class implements CastsAttributes
        {
            private readonly JSONDecoder $decoder;
            private readonly JSONEncoder $encoder;

            public function __construct()
            {
                $this->decoder = new JSONDecoder();
                $this->encoder = new JSONEncoder();
            }

            public function get(Model $model, string $key, mixed $value, array $attributes): ?Condition
            {
                return $value === null ? null : $this->decoder->decode($value)->decodeObject(Condition::class);
            }

            public function set(Model $model, string $key, mixed $value, array $attributes): array
            {
                return [$key => $this->encoder->encode($value)];
            }
        };
    }

    protected function getFieldValue(string $fieldCode, array $answers, Encrypter $encrypter): mixed
    {
        return null;
    }

    /**
     * @param array<Answer> $answers
     */
    abstract public function evaluate(array $answers, Encrypter $encrypter): bool;
}
