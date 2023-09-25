<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Serialisation\Models\Application;

enum EncryptedResponseError: string
{
    public identityNotFound = ''

    case OK = 200;
    case CREATED = 201;
    case ACCEPTED = 202;
    case NO_CONTENT = 204;

    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;

    case INTERNAL_SERVER_ERROR = 500;
    case SERVICE_UNAVAILABLE = 503;

    public function getStatus(): EncryptedResponseStatus
    {

    }

    public function getCode(): string
    {

    }

    public function getMessage(): string
    {

    }
}
