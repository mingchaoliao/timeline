<?php

use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentImage;
use Faker\Generator as Faker;

$factory->define(EloquentImage::class, function (Faker $faker) {
    return [
        'path' => $faker->unique()->word . '.jpg',
        'description' => $faker->text,
        'original_name' => $faker->word . '.jpg',
    ];
});
