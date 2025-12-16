<?php

namespace App\Providers;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Http\Request;
use App\Models\Admin;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CreatesNewUsers::class, CreateNewUser::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Fortify::loginView(function () {
            return request()->is('admin*')
                ? view('admin.login')
                : view('auth.login');
        });

        Fortify::registerView(function () {
            return view('auth.register');
        });

        Fortify::verifyEmailView(function () {
            return view('auth.verify-email');
        });

        Fortify::authenticateUsing(function (Request $request) {

            if (request()->is('admin*')) {
                // 管理者ログイン処理
                $admin = \App\Models\Admin::where('email', $request->email)->first();

                if (! $admin || ! \Hash::check($request->password, $admin->password)) {
                    throw ValidationException::withMessages([
                        'email' => ['管理者ログイン情報が正しくありません。'],
                    ]);
                }

                Auth::login($admin);
                return $admin;
            }

            return null;
        });
    }
}