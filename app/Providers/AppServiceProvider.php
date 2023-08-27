<?php

namespace App\Providers;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

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
