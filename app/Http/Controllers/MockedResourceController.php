<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class MockedResourceController extends Controller
{
    public function messages(): JsonResponse
    {
        return response()->json(json_decode(file_get_contents(resource_path('messages.json')), true));
    }

    public function requests(): JsonResponse
    {
        return response()->json(json_decode(file_get_contents(resource_path('requests.json')), true));
    }

    public function btv(): JsonResponse
    {
        return response()->json(
            json_decode(
                file_get_contents(resource_path('borstprothesen-transvrouwen.json')),
                true
            )
        );
    }
}
