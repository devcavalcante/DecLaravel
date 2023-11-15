<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Passport::tokensExpireIn(now()->addMinute(60));
        Passport::refreshTokensExpireIn(now()->addMinute(60));
        Passport::personalAccessTokensExpireIn(now()->addMinute(60));
    }
}
