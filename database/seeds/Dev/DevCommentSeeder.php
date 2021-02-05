<?php

use Carbon\Carbon;
use Faker\Factory;
use App\Models\User;
use App\Models\Comment;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DevCommentSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $faker = Factory::create();
    $comments = [];
    Product::all()->each(function ($product) use (&$comments, $faker) {
      User::inRandomOrder()
        ->limit(rand(3, 15))
        ->get()
        ->each(function ($user) use (&$comments, $faker, $product) {
          $comments[] = [
            'product_id' => $product->id,
            'user_id' => $user->id,
            'content' => $faker->realText(),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
          ];
        });
    });
    $commentChunks = array_chunk($comments, 5000);
    foreach ($commentChunks as $chunk) {
      Comment::insert($chunk);
    }
    // Comment::inRandomOrder()
    //   ->limit(rand(3,15))
    //   ->get()
    //   ->each(function ($user) use (&$comments, $faker, $product) {
    //     $comments[] = [
    //       'product_id' => $product->id,
    //       'user_id' => $user->id,
    //       'comment' => $faker->realText(),
    //       'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
    //       'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
    //     ];
    //   });
  }
}
