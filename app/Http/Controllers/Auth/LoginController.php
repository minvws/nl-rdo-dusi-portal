<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Redirector;
use Illuminate\Http\RedirectResponse;

/**
 * LoginController
 */
class LoginController extends Controller
{
    /**
     * Login.
     *
     * @param \Illuminate\Http\Request $request The HTTP request.
     *
     * @return Redirector|RedirectResponse
     */
    public function login(Request $request): Redirector|RedirectResponse
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
