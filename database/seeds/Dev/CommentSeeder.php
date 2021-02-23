<?php

use Carbon\Carbon;
use Faker\Factory;
use App\Models\User;
use App\Models\Comment;
use App\Models\Product;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
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
    $users = User::all();
    Product::all()->each(function ($product) use (&$comments, $faker, $users) {
      $users->random(rand(5, 20))
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
    $replies = [];
    Comment::inRandomOrder()
      ->get()
      ->each(function ($comment) use (&$replies, $faker, $users) {
        if ($faker->boolean(40)) {
          for ($i = 0; $i < rand(1, 3); $i++) {
            $replies[] = [
              'parent_id' => $comment->id,
              'product_id' => $comment->product_id,
              'user_id' => $users->random(1)->first()->id,
              'content' => $faker->realText(),
              'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
              'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ];
          }
        }
      });
    $replyChunks = array_chunk($replies, 5000);
    foreach ($replyChunks as $chunk) {
      Comment::insert($chunk);
    }
  }
}
