<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\OrderCollection;

class OrderController extends Controller
{
  public function index(Request $request)
  {
    $order = Order::with("order_product")
      ->paginate($request->input("per_page", 12));
    return response()->json(new OrderCollection($order));
  }

  public function delete(Product $product)
  {
    $product->delete();
    return response()->noContent();
  }
}
