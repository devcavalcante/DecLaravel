<?php

namespace App\Providers;

use App\Models\Member;
use App\Repositories\Interfaces\MemberRepositoryInterface;
use App\Repositories\Interfaces\TypeGroupRepositoryInterface;
use App\Repositories\Interfaces\TypeUserRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\MemberRepository;
use App\Repositories\TypeGroupRepository;
use App\Repositories\TypeUserRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;
use L5Swagger\L5SwaggerServiceProvider;

/**
 *
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     * return void
     */
    public function register(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );
        $this->app->bind(
            TypeGroupRepositoryInterface::class,
            TypeGroupRepository::class
        );
        $this->app->bind(
            TypeUserRepositoryInterface::class,
            TypeUserRepository::class
        );

        $this->app->bind(
            MemberRepositoryInterface::class,
            MemberRepository::class
        );

        $this->app->register(L5SwaggerServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     * return void
     */
    public function boot(): void
    {
        //
    }
}
