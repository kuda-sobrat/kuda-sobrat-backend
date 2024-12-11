<?php

namespace App\Providers;

use App\Contracts\Interfaces\EventRepositoryInterface;
use App\Contracts\Interfaces\EventShareRepositoryInterface;
use App\Contracts\Interfaces\EventViewRepositoryInterface;
use App\Contracts\Interfaces\InterestRepositoryInterface;
use App\Models\EventShare;
use App\Repositories\EventRepository;
use App\Repositories\EventShareRepository;
use App\Repositories\EventViewRepository;
use App\Repositories\InterestRepository;
use Illuminate\Support\ServiceProvider;

// TODO: Вынести в RepositoryServiceProvider
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(InterestRepositoryInterface::class, InterestRepository::class);
        $this->app->bind(EventRepositoryInterface::class, EventRepository::class);
        $this->app->bind(EventViewRepositoryInterface::class, EventViewRepository::class);
        $this->app->bind(EventShareRepositoryInterface::class, EventShareRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
