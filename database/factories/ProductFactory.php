<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {

    $image = $faker->randomElement([
        'images/1-1.jpg',
        'images/1-2.jpg',
        'images/1-3.jpg',
        'images/1-4.jpg',
        'images/2-1.jpg',
        'images/2-2.jpg',
        'images/2-3.jpg',
        'images/3-1.jpg',
        'images/3-2.jpg',
        'images/3-3.jpg',
        'images/3-4.jpg',
    ]);

    return [
        'title'        => $faker->word,
        'description'  => $faker->sentence,
        'image'        => $image,
        'on_sale'      => true,
        'rating'       => $faker->numberBetween(0, 5),
        'sold_count'   => $faker->numberBetween(0, 100),
        'review_count' => $faker->numberBetween(0, 100),
        'price'        => 0,
    ];

});
