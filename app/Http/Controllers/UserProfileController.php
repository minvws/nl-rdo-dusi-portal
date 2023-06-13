<?php

declare(strict_types=1);

namespace App\Http\Controllers;

class UserProfileController extends Controller
{
    public function __invoke()
    {
        return view('profile.show');
    }
}
