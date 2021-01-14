<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Support\Arr;
use Illuminate\Database\Seeder;

class WishlistSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $products = Product::all()->pluck('id')->toArray();
    $wishlists = [];
    User::all()->each(function ($user) use (&$wishlists, $products) {
      foreach (Arr::wrap(array_rand($products, rand(3, 20))) as $product) {
        $wishlists[] = [
          'product_id' => $products[$product],
          'user_id' => $user->id
        ];
      }
    });
    $wishlistChunks = array_chunk($wishlists, 5000);
    foreach ($wishlistChunks as $chunk) {
      Wishlist::insert($chunk);
    }
  }
}
