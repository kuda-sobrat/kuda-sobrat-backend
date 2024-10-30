<?php

namespace App\Providers;

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

//        app(Factory::class)->register('Base', BaseTransformer::class);
//        app('Dingo\Api\Transformer\Factory')->setAdapter(function ($app) {
//            return new Fractal(new Manager, 'include', ',');
//        });
//        dd(app('Dingo\Api\Transformer\Factory'));
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
