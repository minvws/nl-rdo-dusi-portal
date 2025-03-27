<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Bridge\Shared\DTO;

use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Decoding\Decodable;
use MinVWS\Codable\Decoding\DecodingContainer;
use MinVWS\Codable\Encoding\EncodingContainer;
use MinVWS\Codable\Exceptions\CodablePathException;

/**
 * @template TParams of Codable
 */
class MethodCall implements Codable
{
    public const BINDINGS = self::class . '::BINDINGS';

    /**
     * @param TParams|null $params
     */
    final public function __construct(
        public readonly string $method,
        public readonly ?Codable $params = null
    ) {
    }

    /**
     * @SuppressWarnings(UnusedFormalParameter)
     * @return MethodCall<TParams>
     */
    public static function decode(DecodingContainer $container, ?Decodable $object = null): self
    {
        $method = $container->{'method'}->decodeString();

        $bindings = $container->getContext()->getValue(self::BINDINGS);
        if ($bindings === null) {
            throw new CodablePathException($container->getPath(), 'No value found in context for ' . self::BINDINGS);
        }

        if (!isset($bindings[$method]) || ! $bindings[$method] instanceof Binding) {
            throw new CodablePathException($container->getPath(), 'No valid binding found for method ' . $method);
        }

        $paramsClass = $bindings[$method]->paramsClass;
        if ($paramsClass !== null) {
            $params = $container->{'params'}->decodeObject($paramsClass);
            assert($params instanceof Codable && is_a($params, $paramsClass));
        } else {
            $params = null;
        }

        return new static($method, $params);
    }

    public function encode(EncodingContainer $container): void
    {
        $container->{'method'} = $this->method;

        if (isset($this->params)) {
            $container->{'params'} = $this->params;
        }
    }
}
