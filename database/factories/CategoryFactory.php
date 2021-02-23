<?php

use Faker\Generator;
use App\Models\Category;

$factory->define(Category::class, function (Generator $faker) {
  return [
    'name' => $faker->sentence(2),
    'description' => $faker->paragraph
  ];
});
