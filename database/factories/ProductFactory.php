<?php

use Faker\Generator;
use App\Models\Product;
use App\Models\Category;

$factory->define(Product::class, function (Generator $faker) {
  $name = $faker->unique()->sentence(5);
  $path = '\test\products\featured_image\\';
  return [
    'category_id' => function () {
      return Category::inRandomOrder()->first()->id;
    },
    'name' => $name,
    'description' => $faker->paragraph,
    'price' => $faker->numberBetween(1, 100) * 10000,
    'quantity' => $faker->numberBetween(0, 30),
    'featured_image' => "\storage" . $path . $faker->image(storage_path("app\public" . $path), 450, 450, null, false, false, $name)
  ];
});
