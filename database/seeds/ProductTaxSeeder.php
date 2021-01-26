<?php

use Faker\Factory;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductTaxSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $faker = Factory::create();
    Product::all()->each(function ($product) use ($faker) {
      $product->update([
        "tax_id" => $faker->boolean(70) ? "1" : "2"
      ]);
    });
  }
}
