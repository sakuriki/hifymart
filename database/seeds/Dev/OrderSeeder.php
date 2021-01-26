<?php

use Faker\Factory;
use App\Models\Tax;
use App\Models\User;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\District;
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
    $districts = District::all();
    $districtIds = $districts->pluck('id')->toArray();
    $users = User::all();
    $taxes = Tax::all()->keyBy('id');
    for ($i = 0; $i < 10000; $i++) {
      $user = $faker->boolean(10) ? $users->random() : null;
      $subTotal = 0;
      $tax = 0;
      $orderProduct = [];
      $time = $faker->dateTimeBetween('-9 years', '+1 year');
      foreach (Arr::wrap(array_rand($productIds, rand(1, 3))) as $product) {
        $quantity = rand(1, 3);
        $price = $products[$product]->getSalePrice($time->format('Y-m-d H:i:s'));
        $subTotal += $price * $quantity;
        $tax += $subTotal * $taxes[$products[$product]->tax_id]->value / 100;
        $orderProduct[] = [
          "product_id" => $products[$product]->id,
          "quantity" => $quantity,
          "price" => $price
        ];
      }
      $discount = 0;
      $coupon = $faker->boolean(20) ? Coupon::inRandomOrder()->Redeemable($time, $subTotal)->first() : null;
      if ($coupon && $coupon->value) {
        $discount = $coupon->value;
        if ($coupon->is_percent) {
          $discount = $subTotal * $coupon->value / 100;
          if ($coupon->max) {
            $discount = $discount < $coupon->max
              ? $discount
              : $coupon->max;
          }
        }
        $coupon->number = --$coupon->number;
        $coupon->save();
      }
      $shipping_fee = $subTotal >= 200000 ? 0 : 19000;
      $totalAfterDiscount = $subTotal - $discount;
      $total = $totalAfterDiscount > 0 ? $totalAfterDiscount + $tax + $shipping_fee : $shipping_fee;
      $randDistrict = array_rand($districtIds, 1);
      $order = Order::create([
        'user_id' => $user ? $user->id : null,
        'district_id' => $districtIds[$randDistrict],
        'province_id' => $districts[$randDistrict]->province_id,
        'billing_email' => $user ? $user->email : $faker->safeEmail,
        'billing_name' => $user ? $user->name : $faker->name,
        'billing_address' => $faker->unique()->sentence(2),
        'billing_phone' => $user ? $user->phone : $faker->phoneNumber,
        'billing_discount' => $discount,
        'billing_discount_code' => $coupon ? $coupon->code : null,
        'billing_subtotal' => $subTotal,
        'billing_tax' => $tax,
        'billing_shipping_fee' => $shipping_fee,
        'billing_total' => $total,
        'payment_type' => 'cod',
        'created_at' => $time,
        'updated_at' => $time
      ]);
      $orderProduct = array_map(function ($item) use ($order) {
        $item["order_id"] = $order->id;
        return $item;
      }, $orderProduct);
      OrderProduct::insert($orderProduct);
    };
  }
}
