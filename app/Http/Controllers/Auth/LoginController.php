<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * LoginController
 */
class LoginController extends Controller
{
    /**
     * login
     *
     * @param Request request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request): \Illuminate\Http\RedirectResponse
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
