<?php

use Carbon\Carbon;
use Faker\Factory;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSaleSeeder extends Seeder
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
      if ($faker->boolean(70)) {
        return;
      }
      $product->update([
        "sale_off_price" => $product->price * $faker->numberBetween(10, 90) / 100,
        "sale_off_start" => Carbon::now(),
        "sale_off_end" => Carbon::now()->addDays($faker->numberBetween(1, 90)),
        "sale_off_quantity" => $faker->boolean(30) ? $faker->numberBetween(1, 90) : null
      ]);
    });
  }
}
