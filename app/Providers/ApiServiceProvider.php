<?php

namespace App\Providers;

use App\Services\VkService;
use App\Transformers\BaseTransformer;
use Dingo\Api\Transformer\Adapter\Fractal;
use Dingo\Api\Transformer\Factory;
use Illuminate\Support\ServiceProvider;
use League\Fractal\Manager;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app['Dingo\Api\Transformer\Factory']->setAdapter(function ($app) {
            return new BaseTransformer(new Manager);
        });
        $this->app->singleton(VkService::class, function ($app) {
            return new VkService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
