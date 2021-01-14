<?php

use Faker\Factory;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\District;
use App\Models\Province;
use Illuminate\Support\Arr;
use App\Models\OrderProduct;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $faker = Factory::create();
    $products = Product::all();
    $productIds = $products->pluck('id')->toArray();
    $districts = District::all()->pluck('id')->toArray();
    $provinces = Province::all()->pluck('id')->toArray();
    $users = User::all();
    // $faker->dateTimeBetween('first day of september last year')
    for ($i = 0; $i < 1000; $i++) {
      $user = $faker->boolean(10) ? $users->random() : null;
      $subTotal = 0;
      $orderProduct = [];
      $time = $faker->dateTimeBetween('-1 year -3 months');
      foreach (Arr::wrap(array_rand($productIds, rand(1, 3))) as $product) {
        $quantity = rand(1, 3);
        $subTotal += $products[$product]->getSalePrice($time->format('Y-m-d H:i:s')) * $quantity;
        $orderProduct[] = [
          "product_id" => $products[$product]->id,
          "quantity" => $quantity
        ];
      }
      $discount = 0;
      $tax = $subTotal * 0.1;
      $totalAfterDiscount = $subTotal - $discount;
      $total = $totalAfterDiscount > 0 ? $totalAfterDiscount + $tax : 0;
      $order = Order::create([
        'user_id' => $user ? $user->id : null,
        'district_id' => $districts[array_rand($districts, 1)],
        'province_id' => $provinces[array_rand($provinces, 1)],
        'billing_email' => $user ? $user->email : $faker->safeEmail,
        'billing_name' => $user ? $user->name : $faker->name,
        'billing_address' => $faker->unique()->sentence(2),
        'billing_phone' => $user ? $user->phone : $faker->phoneNumber,
        'billing_discount' => 0,
        'billing_subtotal' => $subTotal,
        'billing_tax' => $tax,
        'billing_total' => $total,
        'payment_type' => 'cod',
        'created_at' => $time,
        'updated_at' => $time
      ]);
      $orderProduct = collect($orderProduct)->map(function ($item) use ($order) {
        $item["order_id"] = $order->id;
        return $item;
      });
      // return $orderProduct;
      OrderProduct::insert($orderProduct->toArray());
    };
    // User::all()->each(function ($user) use ($productIds, $products, $districts, $provinces, $faker) {
    //   $subTotal = 0;
    //   $orderProduct = [];
    //   $time = $faker->dateTimeBetween('first day of september last year');
    //   foreach (Arr::wrap(array_rand($productIds, rand(3, 10))) as $product) {
    //     $quantity = rand(1, 10);
    //     $subTotal += $products[$product]->getSalePrice($time->format('Y-m-d H:i:s')) * $quantity;
    //     $orderProduct[] = [
    //       "product_id" => $products[$product]->id,
    //       "quantity" => $quantity
    //     ];
    //   }
    //   $discount = 0;
    //   $tax = $subTotal * 0.1;
    //   $totalAfterDiscount = $subTotal - $discount;
    //   $total = $totalAfterDiscount > 0 ? $totalAfterDiscount + $tax : 0;
    //   $order = Order::create([
    //     'user_id' => $user->id,
    //     'district_id' => $districts[array_rand($districts, 1)],
    //     'province_id' => $provinces[array_rand($provinces, 1)],
    //     'billing_email' => $user->email,
    //     'billing_name' => $user->name,
    //     'billing_address' => $faker->unique()->sentence(2),
    //     'billing_phone' => $user->phone,
    //     'billing_discount' => 0,
    //     'billing_subtotal' => $subTotal,
    //     'billing_tax' => $tax,
    //     'billing_total' => $total,
    //     'payment_type' => 'cod',
    //     'created_at' => $time,
    //     'updated_at' => $time
    //   ]);
    //   $orderProduct = collect($orderProduct)->map(function ($item) use ($order) {
    //     $item["order_id"] = $order->id;
    //     return $item;
    //   });
    //   // return $orderProduct;
    //   OrderProduct::insert($orderProduct->toArray());
    // });
  }
}
