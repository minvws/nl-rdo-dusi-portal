<?php

namespace App\Http\Controllers;

use App\Services\FormService;
use Illuminate\Http\JsonResponse;

class FormController extends Controller
{
    public function index(FormService $formService): JsonResponse
    {
        $json = $formService->getActiveForms();
        return JsonResponse::fromJsonString($json);
    }

    public function show(string $id, FormService $formService): JsonResponse
    {
        $json = $formService->getFormSchema($id);
        abort_if($json === null, 404);
        return JsonResponse::fromJsonString($json);
    }
}
