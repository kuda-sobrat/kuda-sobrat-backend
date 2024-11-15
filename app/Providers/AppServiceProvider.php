<?php

namespace App\Providers;

use App\Contracts\Interfaces\EventRepositoryInterface;
use App\Contracts\Interfaces\InterestRepositoryInterface;
use App\Repositories\EventRepository;
use App\Repositories\InterestRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(InterestRepositoryInterface::class, InterestRepository::class);
        $this->app->bind(EventRepositoryInterface::class, EventRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
