<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Http\Helpers;

use Illuminate\Http\Request;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ClientPublicKey;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ClientPublicKeyHelper
{
    public const HEADER_NAME = 'X-Dus-I-Public-Key';

    public function __construct(private readonly Request $request)
    {
    }

    public function hasClientPublicKey(): bool
    {
        return $this->request->headers->has(self::HEADER_NAME);
    }

    /**
     * @throws HttpException
     */
    public function requireClientPublicKey(): void
    {
        if (!$this->hasClientPublicKey()) {
            abort(400, sprintf('Missing %s header', self::HEADER_NAME));
        }
    }

    /**
     * @throws HttpException
     */
    public function getClientPublicKey(): ClientPublicKey
    {
        $this->requireClientPublicKey();

        $header = $this->request->headers->get(self::HEADER_NAME);
        assert(is_string($header));

        $publicKey = base64_decode($header, true);
        if ($publicKey === false) {
            abort(
                400,
                sprintf(
                    'Invalid %s header, make sure it is base64 encoded',
                    self::HEADER_NAME
                )
            );
        }

        return new ClientPublicKey($publicKey);
    }
}
