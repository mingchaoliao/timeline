<?php

namespace App\Providers;

use App\Timeline\Domain\Repositories\CatalogRepository;
use App\Timeline\Domain\Repositories\DateAttributeRepository;
use App\Timeline\Domain\Repositories\DateFormatRepository;
use App\Timeline\Domain\Repositories\EventRepository;
use App\Timeline\Domain\Repositories\ImageRepository;
use App\Timeline\Domain\Repositories\PeriodRepository;
use App\Timeline\Domain\Repositories\UserRepository;
use App\Timeline\Domain\Services\CatalogService;
use App\Timeline\Domain\Services\DateAttributeService;
use App\Timeline\Domain\Services\DateFormatService;
use App\Timeline\Domain\Services\EventService;
use App\Timeline\Domain\Services\PeriodService;
use App\Timeline\Domain\Services\UserService;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentCatalog;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentDateAttribute;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentDateFormat;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentEvent;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentImage;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentPeriod;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentUser;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentCatalogRepository;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentDateAttributeRepository;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentDateFormatRepository;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentEventRepository;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentImageRepository;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentPeriodRepository;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentUserRepository;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\ConnectionInterface;
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
        $eloquentCatalogRepository = new EloquentCatalogRepository(
            resolve(EloquentCatalog::class)
        );

        $eloquentPeriodRepository = new EloquentPeriodRepository(
            resolve(EloquentPeriod::class)
        );

        $eloquentDateAttributeRepository = new EloquentDateAttributeRepository(
            resolve(EloquentDateAttribute::class)
        );

        $eloquentDateFormatRepository = new EloquentDateFormatRepository(
            resolve(EloquentDateFormat::class)
        );

        $eloquentImageRepository = new EloquentImageRepository(
            resolve(EloquentImage::class)
        );

        $this->app->singleton(CatalogRepository::class, function () use ($eloquentCatalogRepository) {
            return $eloquentCatalogRepository;
        });

        $this->app->singleton(PeriodRepository::class, function () use ($eloquentPeriodRepository) {
            return $eloquentPeriodRepository;
        });

        $this->app->singleton(DateAttributeRepository::class, function () use ($eloquentDateAttributeRepository) {
            return $eloquentDateAttributeRepository;
        });

        $this->app->singleton(DateFormatRepository::class, function () use ($eloquentDateFormatRepository) {
            return $eloquentDateFormatRepository;
        });

        $this->app->singleton(ImageRepository::class, function () use ($eloquentImageRepository) {
            return $eloquentImageRepository;
        });

        $this->app->singleton(EventRepository::class, function () use (
            $eloquentImageRepository,
            $eloquentCatalogRepository,
            $eloquentDateAttributeRepository,
            $eloquentDateFormatRepository,
            $eloquentPeriodRepository
        ) {
            return new EloquentEventRepository(
                resolve(EloquentEvent::class),
                $eloquentImageRepository,
                $eloquentCatalogRepository,
                $eloquentDateAttributeRepository,
                $eloquentDateFormatRepository,
                $eloquentPeriodRepository,
                resolve(ConnectionInterface::class)
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
                resolve(UserService::class)
            );
        });

        $this->app->singleton(DateAttributeService::class, function () {
            return new DateAttributeService(
                resolve(DateAttributeRepository::class),
                resolve(UserService::class)
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
                resolve(UserService::class)
            );
        });

        $this->app->singleton(EventService::class, function () {
            return new EventService(
                resolve(EventRepository::class),
                resolve(UserService::class)
            );
        });
    }
}
