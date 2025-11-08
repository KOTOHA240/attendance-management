<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        $user = \App\Models\User::where('email', $validated['email'])->first();

        if (! $user || ! \Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['ログイン情報が登録されていません。'],
            ]);
        }

        Auth::login($user);

        return redirect()->intended('/attendance');
    }
}
