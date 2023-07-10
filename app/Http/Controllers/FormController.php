<?php

namespace App\Http\Controllers;

use App\Models\FormData;
use App\Services\FormService;
use Illuminate\Http\JsonResponse;

class FormController extends Controller
{
    public function show(FormData $form, FormService $formService): JsonResponse
    {
        return JsonResponse::fromJsonString($form->json);
    }
}
