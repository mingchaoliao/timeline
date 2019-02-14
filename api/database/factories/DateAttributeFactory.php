<?php

use Faker\Generator as Faker;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentDateAttribute;

$factory->define(EloquentDateAttribute::class, function (Faker $faker) {
    return [
        'value' => $faker->unique()->word
    ];
});
