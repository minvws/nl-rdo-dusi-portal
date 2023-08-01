<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function info(): JsonResponse
    {
        $user = Auth::user();
        return JsonResponse::fromJsonString(
            json_encode(
                [
                'logged_in' => $user !== null,
                ]
            )
        );
    }

    public function logout(): JsonResponse
    {
        Auth::logout();
        return $this->info();
    }
}
