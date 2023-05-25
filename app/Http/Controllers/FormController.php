<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class FormController extends Controller
{
    public function show(): JsonResponse
    {
        $form = json_decode(file_get_contents(base_path('resources/mocks/form.json')));
        return response()->json($form, options: JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
