<?php

use Faker\Generator;
use App\Models\Product;

$factory->define(Product::class, function (Generator $faker) {
  $name = $faker->sentence(5);
  $path = '\test\products\featured_image\\';
  return [
    'name' => $name,
    'description' => $faker->paragraph,
    'content' => $faker->paragraphs(5, true),
    'price' => $faker->numberBetween(1, 100) * 10000,
    'quantity' => $faker->numberBetween(0, 30),
    'featured_image' => "\storage" . $path . $faker->image(storage_path("app\public" . $path), 450, 450, null, false, false, $name)
  ];
});
