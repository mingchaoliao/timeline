<?php

use Faker\Generator as Faker;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentPeriod;

$factory->define(EloquentPeriod::class, function (Faker $faker) {
    return [
        'value' => $faker->unique()->word
    ];
});
