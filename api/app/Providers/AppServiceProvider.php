<?php

namespace App\Providers;

use App\Common\Authorization;
use App\Repositories\CatalogRepository;
use App\Repositories\DateAttributeRepository;
use App\Repositories\DateFormatRepository;
use App\Repositories\ImportEventRepository;
use App\Repositories\PeriodRepository;
use App\Repositories\ImageRepository;
use App\Repositories\EventRepository;
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
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        $this->app->singleton(Authorization::class, function ($pp) {
            return new Authorization();
        });

        $this->app->singleton(CatalogRepository::class, function ($app) {
            return new CatalogRepository(resolve(Authorization::class));
        });

        $this->app->singleton(DateAttributeRepository::class, function ($app) {
            return new DateAttributeRepository(resolve(Authorization::class));
        });

        $this->app->singleton(DateFormatRepository::class, function ($app) {
            return new DateFormatRepository(resolve(Authorization::class));
        });

        $this->app->singleton(PeriodRepository::class, function ($app) {
            return new PeriodRepository(resolve(Authorization::class));
        });

        $this->app->singleton(ImageRepository::class, function ($app) {
            return new ImageRepository(resolve(Authorization::class));
        });

        $this->app->singleton(EventRepository::class, function ($app) {
            return new EventRepository(
                resolve(Authorization::class),
                resolve(CatalogRepository::class),
                resolve(DateAttributeRepository::class),
                resolve(DateFormatRepository::class),
                resolve(PeriodRepository::class),
                resolve(ImageRepository::class)
            );
        });
    }
}
