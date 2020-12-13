<?php

use App\Models\User;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;

class DevDatabaseSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    factory(Brand::class, 10)->create();
    factory(Tag::class, 100)->create();
    factory(Category::class, 30)->create();
    factory(User::class, 30)->create();
    Brand::all()->each(function ($brand) {
      $products = factory(Product::class, rand(5, 15))->make();
      $brand->products()->saveMany($products);
    });
    $tags = Tag::all();
    Product::all()->each(function ($product) use ($tags) {
      $product->tags()->attach(
        $tags->random(rand(5, 15))->pluck('id')->toArray()
      );
    });
  }
}
