<?php

use Faker\Generator;
use App\Models\ProductImage;

$factory->define(ProductImage::class, function (Generator $faker) {
  $path = '/test/products/product_images/';
  return [
    'url' => "/storage" . $path . $faker->image(storage_path("app/public" . $path), 450, 450, null, false, true)
  ];
});
