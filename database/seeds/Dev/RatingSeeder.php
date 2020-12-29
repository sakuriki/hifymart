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
    $positiveRating = [5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 3, 3, 3, 2, 2, 1, 1, 1, 1, 1];
    $negativeRating = [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 5, 5, 5];
    Product::all()->each(function ($product) use (&$ratings, $faker, $positiveRating, $negativeRating) {
      $randomRating = $faker->boolean(80) ? $positiveRating : $negativeRating;
      User::all()->each(function ($user) use (&$ratings, $faker, $randomRating, $product) {
        $ratings[] = [
          'product_id' => $product->id,
          'approved' => $faker->boolean(80),
          'user_id' => $user->id,
          'rating' => $randomRating[array_rand($randomRating)],
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
