<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Subsidy\Admin\API\Http\Controllers\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use MinVWS\DUSi\Subsidy\Admin\API\Http\Controllers\Controller;

class LoginController extends Controller
{
    //
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Authentication passed, redirect to a secure page
            return redirect()->intended('/');
        } else {
            // Authentication failed, redirect back to the login form
            // return redirect()->back()->withErrors(['message' => 'Invalid credentials']);
            return redirect()->intended('/');
        }
    }
}
