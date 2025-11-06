<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->intended(RouteServiceProvider::ADMIN_HOME);
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }
}
