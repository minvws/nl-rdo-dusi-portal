<?php

namespace App\Http\Controllers;

use App\Models\FormData;
use Illuminate\Http\JsonResponse;

class FormController extends Controller
{
    public function show(FormData $form): JsonResponse
    {
        return JsonResponse::fromJsonString($form->json);
    }
}
