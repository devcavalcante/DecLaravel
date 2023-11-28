<?php

namespace App\Providers;

use App\Repositories\ActivityRepository;
use App\Repositories\Interfaces\ActivityRepositoryInterface;
use App\Repositories\Interfaces\MeetingRepositoryInterface;
use App\Repositories\DocumentRepository;
use App\Repositories\Interfaces\DocumentRepositoryInterface;
use App\Repositories\Interfaces\MemberRepositoryInterface;
use App\Repositories\GroupHasRepresentativeRepository;
use App\Repositories\GroupRepository;
use App\Repositories\Interfaces\GroupHasRepresentativeRepositoryInterface;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use App\Repositories\Interfaces\NoteRepositoryInterface;
use App\Repositories\Interfaces\TypeGroupRepositoryInterface;
use App\Repositories\Interfaces\TypeUserRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\MeetingRepository;
use App\Repositories\MemberRepository;
use App\Repositories\NoteRepository;
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
            GroupRepositoryInterface::class,
            GroupRepository::class
        );
        $this->app->bind(
            GroupHasRepresentativeRepositoryInterface::class,
            GroupHasRepresentativeRepository::class
        );
        $this->app->bind(
            MemberRepositoryInterface::class,
            MemberRepository::class
        );
        $this->app->bind(
            DocumentRepositoryInterface::class,
            DocumentRepository::class
        );
        $this->app->bind(
            MeetingRepositoryInterface::class,
            MeetingRepository::class
        );
        $this->app->bind(
            ActivityRepositoryInterface::class,
            ActivityRepository::class
        );
        $this->app->bind(
            NoteRepositoryInterface::class,
            NoteRepository::class
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
