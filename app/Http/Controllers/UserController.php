<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function user(): JsonResponse
    {
        $user = Auth::user();
        return JsonResponse::fromJsonString(
            json_encode([
                'logged_in' => $user !== null,
            ])
        );
    }
}
