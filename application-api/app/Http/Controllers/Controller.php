<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\API\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\EncryptedResponse;
use MinVWS\Logging\Laravel\LogService;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    public const ENCRYPTED_HEADER_NAME = 'X-Dus-I-Encrypted';

    public function __construct(
        protected readonly LogService $logger
    ) {
    }

    protected function encryptedResponse(EncryptedResponse $response): Response
    {
        $headers = [
            // On the client side we can't modify the headers of a request, so we need to set the final response
            // type even though the data first needs to be decrypted to comply.
            'Content-Type' => $response->contentType,
            self::ENCRYPTED_HEADER_NAME => 'true'
        ];

        $data = $response->data;

        // On the client side useFetch doesn't let us easily intercept the response before it is decoded
        // to a certain type. This will result in an error if we return a binary blob even though the final
        // result is JSON. So for JSON response we create a JSON string instead.
        if ($response->contentType === 'application/json') {
            $data = json_encode(base64_encode($response->data));
            assert(is_string($data));
        }

        $result = response($data, $response->status->value, $headers);
        assert($result instanceof Response);
        return $result;
    }
}
