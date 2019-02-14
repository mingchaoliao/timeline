<?php

use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentUser;
use Faker\Generator as Faker;

$factory->define(EloquentUser::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->name,
        'email' => $faker->unique()->email,
        'password' => $faker->password,
        'is_admin' => $faker->numberBetween(0, 1),
        'is_editor' => $faker->numberBetween(0, 1),
    ];
});
