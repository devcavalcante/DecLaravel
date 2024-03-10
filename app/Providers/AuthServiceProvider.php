<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Passport::tokensExpireIn(now()->addHours(24));
        Passport::refreshTokensExpireIn(now()->addHours(24));
        Passport::personalAccessTokensExpireIn(now()->addHours(24));
        ResetPassword::createUrlUsing(function (User $user, string $token) {
            return sprintf('%s/nova-senha?token=%s&email=%s', env('FRONT_URL'), $token, $user->email);
        });
    }
}
