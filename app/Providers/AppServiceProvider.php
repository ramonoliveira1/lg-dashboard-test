<?php

namespace App\Providers;

use App\Repositories\ProductivityRepository;
use App\Repositories\ProductivityRepositoryInterface;
use App\Services\GeminiService;
use App\Services\GeminiServiceInterface;
use App\Services\ProductivityService;
use App\Services\ProductivityServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            ProductivityRepositoryInterface::class,
            ProductivityRepository::class
        );

        $this->app->bind(
            ProductivityServiceInterface::class,
            ProductivityService::class
        );

        $this->app->bind(
            GeminiServiceInterface::class,
            GeminiService::class
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
