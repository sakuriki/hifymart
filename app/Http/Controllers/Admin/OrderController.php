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
    $user = auth()->user();
    if (!$user || $user->cannot('order.access')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $orders = Order::where(function ($query) use ($request) {
      $query->where('id', 'LIKE', "%{$request->input('q')}%")
        ->orWhere('billing_name', 'LIKE', "%{$request->input('q')}%");
    })
      ->select(['id', 'user_id', 'district_id', 'province_id', 'billing_email', 'billing_name', 'billing_address', 'billing_phone', 'billing_total', 'is_paid', 'status', 'shipped'])
      ->with(["province", "district", "user:id,name,email,phone"])
      ->withCount('order_product');
    $sortBy = $request->input("sortBy");
    $sortDesc = $request->input("sortDesc") == "false" ? "asc" : "desc";
    switch ($sortBy) {
      case "id":
        $orders = $orders->orderBy("id", $sortDesc);
        break;
      case "billing_email":
        $orders = $orders->orderBy("billing_email", $sortDesc);
        break;
      case "billing_name":
        $orders = $orders->orderBy("billing_name", $sortDesc);
        break;
      case "billing_phone":
        $orders = $orders->orderBy("billing_phone", $sortDesc);
        break;
      case "billing_total":
        $orders = $orders->orderBy("billing_total", $sortDesc);
        break;
      case "is_paid":
        $orders = $orders->orderBy("is_paid", $sortDesc);
        break;
      case "shipped":
        $orders = $orders->orderBy("shipped", $sortDesc);
        break;
      case "status":
        $orders = $orders->orderBy("status", $sortDesc);
        break;
      case "order_product_count":
        $orders = $orders->orderBy("order_product_count", $sortDesc);
        break;
      default:
        $orders = $orders->latest();
    }
    $orders = $orders->paginate($request->input("per_page", 12));
    $orders->setCollection($orders->getCollection()->makeHidden(['user_id', 'district_id', 'province_id']));
    return response()->json(new OrderCollection($orders));
  }

  public function delete(Product $product)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('order.delete')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $product->delete();
    return response()->noContent();
  }
}
