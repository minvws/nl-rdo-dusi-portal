<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplicationSubmitRequest;
use App\Http\Requests\ApplicationUploadFileRequest;
use App\Models\Application;
use App\Models\DraftApplication;
use App\Models\FormData;
use App\Services\ApplicationService;
use App\Services\Exceptions\FormNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\ResponseFactory;
use Throwable;

class ApplicationController extends Controller
{
    public function createDraft(FormData $form, ApplicationService $applicationService): JsonResponse
    {
        try {
            $id = $applicationService->createDraft($form);
            return response()->json(['id' => $id], status: 202);
        } catch (FormNotFoundException $e) {
            abort(404, $e->getMessage());
        }
    }

    /**
     * @throws Throwable
     */
    public function uploadFile(Application $application, ApplicationUploadFileRequest $request, ApplicationService $applicationService): JsonResponse
    {
        $fieldCode = $request->safe()->{'fieldCode'};
        assert(is_string($fieldCode));
        $file = $request->safe()->{'file'};
        assert($file instanceof UploadedFile);
        $id = $applicationService->uploadFile($application, $fieldCode, $file);
        return response()->json(['id' => $id], status: 202);
    }

    public function submit(Application $application, ApplicationSubmitRequest $request, ApplicationService $applicationService): Response|ResponseFactory
    {
        $encryptedData = $request->safe()->{'data'};
        assert(is_string($encryptedData));
        $applicationService->submit($application, $encryptedData);
        return response(status: 202);
    }
}
