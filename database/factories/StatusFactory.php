<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Status;
use Faker\Generator as Faker;

$factory->define(Status::class, function (Faker $faker) {
    return [
        'title' => $faker->word,
        'color_classes' => $faker->randomElement([
            'label-green',
            'label-yellow',
            'label-orange',
            'label-red',
            'label-purple',
            'label-blue',
            'label-sky',
            'label-lime',
            'label-pink',
            'label-black',
            'label-default',
        ]),
    ];
});
