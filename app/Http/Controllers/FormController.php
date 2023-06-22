<?php

namespace App\Http\Controllers;

use App\Services\FormService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Ramsey\Uuid\Uuid;

class FormController extends Controller
{
    public function show(string $id, FormService $formService): JsonResponse
    {
        $json = $formService->getForm($id);
        abort_if($json === null, 404);
        return JsonResponse::fromJsonString($json);
    }

    public function submit(string $id, Request $request, FormService $formService): Response|ResponseFactory
    {
        $formService->submitForm($id, $request->getContent());
        return response(status: 204);
    }

    public function uploadFile(string $formId, Request $request, FormService $formService): Response|ResponseFactory
    {
        $fileId = Uuid::uuid4()->toString();
        $formService->uploadFile($formId, $fileId, $request->getContent());
        return response(status: 204);
    }
}
