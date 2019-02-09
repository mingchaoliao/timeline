<?php

use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentEvent;
use Faker\Generator as Faker;

$factory->define(EloquentEvent::class, function (Faker $faker) {
    return [
        'start_date_str' => $faker->name,
        'start_date' => $faker->name,
        'start_date_attribute_id' => $faker->name,
        'end_date_str' => $faker->name,
        'end_date' => $faker->name,
        'end_date_attribute_id' => $faker->name,
        'content' => $faker->name,
        'period_id' => $faker->name,
        'create_user_id' => $faker->name,
        'update_user_id' => $faker->name,
    ];
});
