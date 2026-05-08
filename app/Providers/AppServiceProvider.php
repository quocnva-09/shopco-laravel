<?php

namespace App\Providers;

use App\Contracts\AuthServiceInterface;
use App\Contracts\CategoryServiceInterface;
use App\Contracts\ProductServiceInterface;
use App\Contracts\UserServiceInterface;
use App\Services\AuthService;
use App\Services\CategoryService;
use App\Services\ProductService;
use App\Services\UserService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CategoryServiceInterface::class, CategoryService::class);
        $this->app->bind(ProductServiceInterface::class, ProductService::class);
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
