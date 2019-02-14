<?php

use Faker\Generator as Faker;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentCatalog;

$factory->define(EloquentCatalog::class, function (Faker $faker) {
    return [
        'value' => $faker->unique()->word
    ];
});
