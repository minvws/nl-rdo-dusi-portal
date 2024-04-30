<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Models\Condition;

use Illuminate\Contracts\Database\Eloquent\Castable;
use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Coding\CodableSupport;
use MinVWS\Codable\Decoding\Decodable;
use MinVWS\Codable\Decoding\DecodingContainer;
use MinVWS\Codable\Encoding\EncodingContainer;
use MinVWS\Codable\Exceptions\CodableException;
use MinVWS\DUSi\Shared\Serialisation\Casts\CodableCast;

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

    /**
     * @param array $arguments
     * @return CodableCast<Condition>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public static function castUsing(array $arguments): CodableCast
    {
        return new CodableCast(self::class);
    }


    /**
     * @param array<int, object> $data Data by stage.
     */
    abstract public function evaluate(array $data): bool;
}
