<?php

namespace App\Providers;

use App\Timeline\Domain\Repositories\CatalogRepository;
use App\Timeline\Domain\Repositories\DateAttributeRepository;
use App\Timeline\Domain\Repositories\DateFormatRepository;
use App\Timeline\Domain\Repositories\PeriodRepository;
use App\Timeline\Domain\Repositories\UserRepository;
use App\Timeline\Domain\Services\CatalogService;
use App\Timeline\Domain\Services\DateAttributeService;
use App\Timeline\Domain\Services\DateFormatService;
use App\Timeline\Domain\Services\PeriodService;
use App\Timeline\Domain\Services\UserService;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentCatalog;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentDateAttribute;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentDateFormat;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentPeriod;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentUser;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentCatalogRepository;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentDateAttributeRepository;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentDateFormatRepository;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentPeriodRepository;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentUserRepository;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Facades\Auth;
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
            return new EloquentCatalogRepository(
                resolve(EloquentCatalog::class)
            );
        });

        $this->app->singleton(PeriodRepository::class, function () {
            return new EloquentPeriodRepository(
                resolve(EloquentPeriod::class)
            );
        });

        $this->app->singleton(DateAttributeRepository::class, function () {
            return new EloquentDateAttributeRepository(
                resolve(EloquentDateAttribute::class)
            );
        });

        $this->app->singleton(DateFormatRepository::class, function () {
            return new EloquentDateFormatRepository(
                resolve(EloquentDateFormat::class)
            );
        });

        $this->app->singleton(UserRepository::class, function () {
            return new EloquentUserRepository(
                resolve(EloquentUser::class),
                resolve(Hasher::class),
                Auth::guard('api')
            );
        });

        $this->app->singleton(UserService::class, function () {
            return new UserService(
                resolve(UserRepository::class)
            );
        });

        $this->app->singleton(CatalogService::class, function () {
            return new CatalogService(
                resolve(CatalogRepository::class),
                resolve(UserRepository::class)
            );
        });

        $this->app->singleton(DateAttributeService::class, function () {
            return new DateAttributeService(
                resolve(DateAttributeRepository::class),
                resolve(UserRepository::class)
            );
        });

        $this->app->singleton(DateFormatService::class, function () {
            return new DateFormatService(
                resolve(DateFormatRepository::class)
            );
        });

        $this->app->singleton(PeriodService::class, function () {
            return new PeriodService(
                resolve(PeriodRepository::class),
                resolve(UserRepository::class)
            );
        });
    }
}
