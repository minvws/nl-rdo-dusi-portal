<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    //
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->only('name', 'password');

        if (Auth::attempt($credentials)) {
            // Authentication passed, redirect to a secure page
            return redirect()->intended('/logged_in');
        } else {
            // Authentication failed, redirect back to the login form
            // return redirect()->back()->withErrors(['message' => 'Invalid credentials']);
            return redirect()->intended('/');
        }
    }
}
