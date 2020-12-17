<?php

use Faker\Factory;
use App\Models\User;
use App\Models\Rating;
use App\Models\Product;
use Illuminate\Database\Seeder;

class RatingSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $faker = Factory::create();
    $ratings = [];
    Product::all()->each(function ($product) use (&$ratings, $faker) {
      User::all()->each(function ($user) use (&$ratings, $faker, $product) {
        $ratings[] = [
          'product_id' => $product->id,
          'approved' => $faker->boolean(80),
          'user_id' => $user->id,
          'rating' => $faker->numberBetween(1, 5),
          'review' => $faker->realText()
        ];
      });
    });
    $ratingChunks = array_chunk($ratings, 5000);
    foreach ($ratingChunks as $chunk) {
      Rating::insert($chunk);
    }
  }
}
