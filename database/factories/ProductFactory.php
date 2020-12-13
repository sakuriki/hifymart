<?php

use Faker\Generator;
use App\Models\Product;
use App\Models\Category;

$factory->define(Product::class, function (Generator $faker) {
  $title = $faker->unique()->sentence(5);
  return [
    'category_id' => function () {
      return Category::inRandomOrder()->first()->id;
    },
    'title' => $title,
    'description' => $faker->paragraph,
    'price' => $faker->numberBetween(1, 100) * 10000,
    'quantity' => $faker->numberBetween(0, 30),
    'featured_image' => $faker->image(storage_path('app\public\featured_image'), 325, 450, null, false, false, $title)
  ];
});
