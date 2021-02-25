<?php

namespace App\Observers;

use App\Models\Order;
use App\Mail\SendInvoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderObserver
{
  /**
   * Listen to the Order created event.
   *
   * @param  \App\Models\Order  $order
   * @return void
   */
  public function created(Order $order): void
  {
    Mail::to($order->billing_email)->queue(new SendInvoice($order));
  }

  /**
   * Listen to the Order updating event.
   *
   * @param  \App\Order  $order
   * @return void
   */
  public function updating(Order $order)
  {
    if ($order->isDirty('status')) {
      if ($order->status == 3) {
        try {
          DB::beginTransaction();
          $order_products = OrderProduct::select('product_id', 'quantity')
            ->where('order_id', $order->id)
            ->get()
            ->keyBy('product_id');
          $products = Product::whereIn("id", $order_products->pluck('product_id'))->get();
          foreach ($products as $product) {
            $product->quantity = $product->quantity - $order_products[$product->id]['quantity'];
            $product->save();
          }
          DB::commit();
        } catch (\Exception $exception) {
          DB::rollBack();
          throw ValidationException::withMessages([
            'message' => 'Có lỗi khi cập nhật số lượng sản phẩm còn trong kho, vui lòng thử lại'
          ]);
        }
      }
    }
  }

  /**
   * Listen to the Order deleting event.
   *
   * @param  \App\Order  $order
   * @return void
   */
  public function deleting(Order $order)
  {
    try {
      DB::beginTransaction();
      $order_products = OrderProduct::select('product_id', 'quantity')
        ->where('order_id', $order->id)
        ->get()
        ->keyBy('product_id');
      $products = Product::whereIn("id", $order_products->pluck('product_id'))->get();
      foreach ($products as $product) {
        $product->quantity = $product->quantity + $order_products[$product->id]['quantity'];
        $product->save();
      }
      DB::commit();
    } catch (\Exception $exception) {
      DB::rollBack();
      throw ValidationException::withMessages([
        'message' => 'Có lỗi khi cập nhật số lượng sản phẩm còn trong kho, vui lòng thử lại'
      ]);
    }
  }

  private function updateQuantity($order)
  {
    # code...
  }
}
