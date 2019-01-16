<?php

namespace App\Providers;

use App\Common\Authorization;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentCatalogRepository;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentDateAttributeRepository;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentDateFormatRepository;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentEventRepository;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentImageRepository;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentPeriodRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Authorization::class, function ($pp) {
            return new Authorization();
        });



        $this->app->singleton(EloquentDateAttributeRepository::class, function ($app) {
            return new EloquentDateAttributeRepository(resolve(Authorization::class));
        });

        $this->app->singleton(EloquentDateFormatRepository::class, function ($app) {
            return new EloquentDateFormatRepository(resolve(Authorization::class));
        });

        $this->app->singleton(EloquentPeriodRepository::class, function ($app) {
            return new EloquentPeriodRepository(resolve(Authorization::class));
        });

        $this->app->singleton(EloquentImageRepository::class, function ($app) {
            return new EloquentImageRepository(resolve(Authorization::class));
        });

        $this->app->singleton(EloquentEventRepository::class, function ($app) {
            return new EloquentEventRepository(
                resolve(Authorization::class),
                resolve(EloquentCatalogRepository::class),
                resolve(EloquentDateAttributeRepository::class),
                resolve(EloquentDateFormatRepository::class),
                resolve(EloquentPeriodRepository::class),
                resolve(EloquentImageRepository::class)
            );
        });
    }
}
