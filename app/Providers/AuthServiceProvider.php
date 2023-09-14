<?php

namespace App\Providers;

 use App\Models\User;
 use App\Policies\UserPolicy;
 use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Passport::tokensExpireIn(now()->addMinute(60));
        Passport::refreshTokensExpireIn(now()->addMinute(60));
        Passport::personalAccessTokensExpireIn(now()->addMinute(60));
    }
}
