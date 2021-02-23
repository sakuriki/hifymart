<?php

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
    $this->call([
      BrandSeeder::class,
      CategorySeeder::class,
      TagSeeder::class,
      UserSeeder::class,
      ProductSeeder::class,
      ProductSaleSeeder::class,
      CommentSeeder::class,
      CouponSeeder::class,
      OrderSeeder::class,
      RatingSeeder::class,
      WishlistSeeder::class
    ]);
  }
}
