<?php

use Faker\Generator;
use App\Models\Brand;

$factory->define(Brand::class, function (Generator $faker) {
  return [
    'name' => $faker->sentence(2),
    'description' => $faker->paragraph
  ];
});
