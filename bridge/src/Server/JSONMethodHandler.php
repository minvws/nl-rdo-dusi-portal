<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Bridge\Server;

use JsonException;
use MinVWS\Codable\Coding\Codable;
use MinVWS\Codable\Exceptions\CodableException;
use MinVWS\Codable\Exceptions\PathNotFoundException;
use MinVWS\Codable\Exceptions\ValueNotFoundException;
use MinVWS\Codable\Exceptions\ValueTypeMismatchException;
use MinVWS\Codable\JSON\JSONDecoder;
use MinVWS\Codable\JSON\JSONEncoder;
use MinVWS\DUSi\Shared\Bridge\Shared\DTO\Binding;
use MinVWS\DUSi\Shared\Bridge\Shared\DTO\MethodCall;
use MinVWS\DUSi\Shared\Bridge\Shared\DTO\MethodResult;

readonly class JSONMethodHandler
{
    public function __construct(
        private JSONEncoder $encoder = new JSONEncoder(),
        private JSONDecoder $decoder = new JSONDecoder(),
    ) {
    }

    /**
     * @param string $encodedCall
     * @param array<string, Binding> $bindings
     * @return MethodCall<Codable>
     * @throws CodableException
     * @throws JsonException
     * @throws PathNotFoundException
     * @throws ValueNotFoundException
     * @throws ValueTypeMismatchException
     */
    public function decodeMethodCall(string $encodedCall, array $bindings): MethodCall
    {
        $this->decoder->getContext()->setValue(MethodCall::BINDINGS, $bindings);
        $call = $this->decoder->decode($encodedCall)->decodeObject(MethodCall::class);
        assert($call instanceof MethodCall);
        return $call;
    }

    /**
     * @throws ValueTypeMismatchException
     * @throws JsonException
     */
    public function encodeMethodResult(Codable $data): string
    {
        $methodResult = new MethodResult($data);
        return $this->encoder->encode($methodResult);
    }
}
