<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
  public function store(Request $request)
  {
    // dd($request->all());
    try {
      DB::beginTransaction();
      $coupon = null;
      // $coupon = Coupon::findOrFail($request->coupon);
      $products = Product::whereIn("id", collect($request->input("product"))->keys())->get();
      $subTotal = 0;
      $orderProduct = [];
      foreach ($products as $product) {
        $quantity = $request->input("product")[$product->id];
        $subTotal += $product->price * $quantity;
        $orderProduct[] = [
          "product_id" => $product->id,
          "quantity" => $quantity
        ];
        $newQuantity = $product->quantity - $quantity;
        if ($newQuantity < 0) {
          $text = $product->quantity == 0 ? "đã hết hàng." : 'không đủ số lượng bạn yêu cầu, vui lòng giảm số lượng hoặc mua lại sau.';
          return response()->json([
            'errors' => [
              'quantity' => ['Xin lỗi, "' . $product->title . '" trong giỏ của bạn hiện ' . $text]
            ]
          ], 422);
        }
        $product->quantity = $newQuantity;
        $product->save();
      }
      $discount = 0;
      if ($coupon) {
        $discount = $coupon->value;
        if ($coupon->is_percent) {
          $discount = $subTotal * $coupon->value / 100;
        }
        if ($coupon->max) {
          $discount = $discount <= $coupon->max
            ? $discount
            : $coupon->max;
        }
        $subTotal = $subTotal - $discount;
        if ($subTotal < 0) {
          $subTotal = 0;
        }
      }
      $tax = $subTotal * 0.1;
      $total = $subTotal + $tax;
      $order = Order::create([
        'user_id' => auth()->user() ? auth()->user()->id : null,
        'billing_email' => $request->email,
        'billing_name' => $request->name,
        'billing_address' => $request->address,
        'billing_district' => $request->district,
        'billing_province' => $request->province,
        'billing_phone' => $request->phone,
        'billing_discount' => $discount,
        // 'billing_discount_code' => $coupon->code,
        'billing_subtotal' => $subTotal,
        'billing_tax' => $tax,
        'billing_total' => $total > 0 ? $total : 0
      ]);
      // return $orderProduct;
      $orderProduct = collect($orderProduct)->map(function ($item) use ($order) {
        $item["order_id"] = $order->id;
        return $item;
      });
      // return $orderProduct;
      OrderProduct::insert($orderProduct->toArray());
      DB::commit();
      return response()->json([
        "order" => $order
      ]);
    } catch (\Exception $exception) {
      DB::rollBack();
      return response()->json([
        "success" => false,
        "errors" => $exception->getMessage()
      ], 422);
    }
  }
}
