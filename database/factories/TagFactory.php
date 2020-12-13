<?php

use App\Models\Tag;
use Faker\Generator;

$factory->define(Tag::class, function (Generator $faker) {
  return [
    'name' => $faker->unique()->sentence(1),
  ];
});
