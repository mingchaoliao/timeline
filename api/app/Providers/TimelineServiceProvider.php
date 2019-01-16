<?php

namespace App\Providers;

use App\Timeline\Domain\Repositories\CatalogRepository;
use App\Timeline\Domain\Repositories\DateAttributeRepository;
use App\Timeline\Domain\Repositories\DateFormatRepository;
use App\Timeline\Domain\Repositories\PeriodRepository;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentCatalogRepository;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentDateAttributeRepository;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentDateFormatRepository;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentPeriodRepository;
use Illuminate\Support\ServiceProvider;

class TimelineServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CatalogRepository::class, function () {
            return new EloquentCatalogRepository();
        });

        $this->app->singleton(PeriodRepository::class, function () {
            return new EloquentPeriodRepository();
        });

        $this->app->singleton(DateAttributeRepository::class, function () {
            return new EloquentDateAttributeRepository();
        });

        $this->app->singleton(DateFormatRepository::class, function () {
            return new EloquentDateFormatRepository();
        });
    }
}
