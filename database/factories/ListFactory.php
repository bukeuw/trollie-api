<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ListModel;
use Faker\Generator as Faker;

$factory->define(ListModel::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'board_id' => 1,
    ];
});
