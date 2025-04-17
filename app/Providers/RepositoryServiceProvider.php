<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Interfaces\Repositories\UserRepositoryInterface;
use App\Domain\Interfaces\Repositories\RoleRepositoryInterface;
use App\Domain\Interfaces\Repositories\EntityTypeRepositoryInterface;
use App\Domain\Interfaces\Repositories\AttributeRepositoryInterface;
use App\Domain\Interfaces\Repositories\AttributeValueRepositoryInterface;
use App\Infrastructure\Repositories\EloquentUserRepository;
use App\Infrastructure\Repositories\EloquentRoleRepository;
use App\Infrastructure\Repositories\EloquentEntityTypeRepository;
use App\Infrastructure\Repositories\EloquentAttributeRepository;
use App\Infrastructure\Repositories\EloquentAttributeValueRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, EloquentRoleRepository::class);
        $this->app->bind(EntityTypeRepositoryInterface::class, EloquentEntityTypeRepository::class);
        $this->app->bind(AttributeRepositoryInterface::class, EloquentAttributeRepository::class);
        $this->app->bind(AttributeValueRepositoryInterface::class, EloquentAttributeValueRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
