<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Application\Services\UserService;
use App\Application\Services\RoleService;
use App\Application\Services\EAVService;

class ApplicationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(UserService::class);
        $this->app->singleton(RoleService::class);
        $this->app->singleton(EAVService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
