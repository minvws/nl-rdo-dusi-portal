<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class MockedResourceController extends Controller
{
    /**
     * @throws \Exception
     */
    public function messages(): JsonResponse
    {
        $contents = file_get_contents(resource_path('messages.json'));
        if ($contents === false) {
            throw new \Exception('Could not read messages.json');
        }
        return response()->json(json_decode($contents, true));
    }

    /**
     * @throws \Exception
     */
    public function requests(): JsonResponse
    {
        $contents = file_get_contents(resource_path('requests.json'));
        if ($contents === false) {
            throw new \Exception('Could not read requests.json');
        }
        return response()->json(json_decode($contents, true));
    }

    /**
     * @throws \Exception
     */
    public function btv(): JsonResponse
    {
        $contents = file_get_contents(resource_path('borstprothesen-transvrouwen.json'));
        if ($contents === false) {
            throw new \Exception('Could not read borstprothesen-transvrouwen.json');
        }
        return response()->json(json_decode($contents, true));
    }
}
