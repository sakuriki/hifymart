<?php

use App\Models\Tag;
use App\Models\User;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

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
    Storage::disk('local')->makeDirectory('public\test\products\featured_image');
    Storage::disk('local')->makeDirectory('public\test\products\product_images');
    Brand::all()->each(function ($brand) {
      $products = factory(Product::class, rand(5, 15))->make();
      $brand->products()->saveMany($products);
    });
    $tags = Tag::all();
    Product::all()->each(function ($product) use ($tags) {
      $product->tags()->attach(
        $tags->random(rand(5, 15))->pluck('id')->toArray()
      );
      factory(ProductImage::class, rand(1, 3))->create([
        'product_id' => $product->id
      ]);
    });
  }
}
