<?php

use App\Models\Tag;
use App\Models\Tax;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ProductSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $categories = Category::all();
    $brands = Brand::all();
    $tags = Tag::all();
    $taxes = Tax::all();
    Storage::disk('local')->deleteDirectory('public/test/products/featured_image');
    Storage::disk('local')->deleteDirectory('public/test/products/product_images');
    Storage::disk('local')->makeDirectory('public/test/products/featured_image');
    Storage::disk('local')->makeDirectory('public/test/products/product_images');
    factory(Product::class, 200)->create()->each(
      function ($product) use ($categories, $brands, $tags, $taxes) {
        $product->tags()->attach($tags->random(rand(5, 10))->pluck('id'));
        $product->category()->associate($categories->random(1)->first()->id);
        $product->brand()->associate($brands->random(1)->first()->id);
        $product->tax()->associate($taxes->random(1)->first()->id);
        $product->save();
        factory(ProductImage::class, rand(1, 3))->create([
          'product_id' => $product->id
        ]);
      }
    );
  }
}
