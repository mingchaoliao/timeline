<?php

use Faker\Generator as Faker;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentUser;

$factory->define(EloquentUser::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->name,
        'email' => $faker->unique()->email,
        'password' => $faker->password,
        'is_admin' => $faker->boolean,
        'is_editor' => $faker->boolean,
    ];
});
