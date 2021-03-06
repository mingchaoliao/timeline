<?php

namespace App\Providers;

use App\Timeline\App\HealthCheckService;
use App\Timeline\App\Validators\ValidatorFactory;
use App\Timeline\Domain\Repositories\CatalogRepository;
use App\Timeline\Domain\Repositories\DateAttributeRepository;
use App\Timeline\Domain\Repositories\EventRepository;
use App\Timeline\Domain\Repositories\ImageFileRepository;
use App\Timeline\Domain\Repositories\ImageRepository;
use App\Timeline\Domain\Repositories\PeriodRepository;
use App\Timeline\Domain\Repositories\SearchEventRepository as SearchEventRepositoryInterface;
use App\Timeline\Domain\Repositories\UserRepository;
use App\Timeline\Domain\Services\BackupService;
use App\Timeline\Domain\Services\CatalogService;
use App\Timeline\Domain\Services\DateAttributeService;
use App\Timeline\Domain\Services\EventService;
use App\Timeline\Domain\Services\ImageService;
use App\Timeline\Domain\Services\PeriodService;
use App\Timeline\Domain\Services\TimelineService;
use App\Timeline\Domain\Services\UserService;
use App\Timeline\Infrastructure\Elasticsearch\SearchEventRepository;
use App\Timeline\Infrastructure\Elasticsearch\SearchParamsBuilder;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentCatalog;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentDateAttribute;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentEvent;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentImage;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentPeriod;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentUser;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentCatalogRepository;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentDateAttributeRepository;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentEventRepository;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentImageRepository;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentPeriodRepository;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentUserRepository;
use App\Timeline\Infrastructure\Persistence\Filesystem\FSImageFileRepository;
use Elasticsearch\Client;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
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
        Validator::extend(
            'event_date',
            'App\Timeline\App\Validators\EventDateValidator@validate'
        );

        Validator::replacer(
            'event_date',
            'App\Timeline\App\Validators\EventDateValidator@message'
        );

        Validator::extend(
            'iso_date',
            'App\Timeline\App\Validators\ISODateValidator@validate'
        );

        Validator::replacer(
            'iso_date',
            'App\Timeline\App\Validators\ISODateValidator@message'
        );

        Validator::extend(
            'date_attribute',
            'App\Timeline\App\Validators\DateAttributeValidator@validate'
        );

        Validator::replacer(
            'date_attribute',
            'App\Timeline\App\Validators\DateAttributeValidator@message'
        );

        Validator::extend(
            'id',
            'App\Timeline\App\Validators\IDValidator@validate'
        );

        Validator::replacer(
            'id',
            'App\Timeline\App\Validators\IDValidator@message'
        );
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

        $eloquentImageRepository = new EloquentImageRepository(
            resolve(EloquentImage::class),
            resolve(ConnectionInterface::class)
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

        $this->app->singleton(ImageRepository::class, function () use ($eloquentImageRepository) {
            return $eloquentImageRepository;
        });

        $this->app->singleton(SearchEventRepositoryInterface::class, function () {
            return new SearchEventRepository(
                resolve(\Elasticsearch\Client::class),
                new SearchParamsBuilder()
            );
        });

        $this->app->singleton(EventRepository::class, function () use (
            $eloquentImageRepository,
            $eloquentCatalogRepository,
            $eloquentDateAttributeRepository,
            $eloquentPeriodRepository
        ) {
            return new EloquentEventRepository(
                resolve(EloquentEvent::class),
                $eloquentImageRepository,
                $eloquentCatalogRepository,
                $eloquentDateAttributeRepository,
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

        $this->app->singleton(ImageFileRepository::class, function () {
            return new FSImageFileRepository(resolve(Filesystem::class));
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

        $this->app->singleton(PeriodService::class, function () {
            return new PeriodService(
                resolve(PeriodRepository::class),
                resolve(UserService::class)
            );
        });

        $this->app->singleton(ImageService::class, function () {
            return new ImageService(
                resolve(ImageRepository::class),
                resolve(ImageFileRepository::class),
                resolve(UserService::class)
            );
        });

        $this->app->singleton(TimelineService::class, function () {
            return new TimelineService(
                resolve(EventRepository::class),
                resolve(Filesystem::class)
            );
        });

        $this->app->singleton(EventService::class, function () {
            return new EventService(
                resolve(EventRepository::class),
                resolve(SearchEventRepositoryInterface::class),
                resolve(UserService::class)
            );
        });

        $this->app->singleton(ValidatorFactory::class, function () {
            return new ValidatorFactory(resolve(Factory::class));
        });

        $this->app->singleton(BackupService::class, function () {
            return new BackupService(
                resolve(\Illuminate\Contracts\Filesystem\Factory::class),
                resolve(Kernel::class),
                resolve(UserService::class)
            );
        });

        $this->app->singleton(HealthCheckService::class, function () {
            return new HealthCheckService(
                resolve(Client::class),
                resolve(SearchEventRepositoryInterface::class),
                resolve(ConnectionInterface::class),
                resolve(Kernel::class)
            );
        });
    }
}
