<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Exports\OrdersExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\Admin\OrderCollection;

class OrderController extends Controller
{
  public function index(Request $request)
  {
    $user = Auth::user();
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
      ->select(['id', 'user_id', 'billing_name', 'billing_total', 'billing_shipping_fee', 'billing_tax', 'payment_type', 'is_paid', 'status', 'shipped'])
      ->with(["user:id,name"])
      ->withCount('order_product');
    $sortBy = $request->input("sortBy");
    $sortDesc = $request->input("sortDesc") == "false" ? "asc" : "desc";
    switch ($sortBy) {
      case "id":
        $orders = $orders->orderBy("id", $sortDesc);
        break;
      case "billing_name":
        $orders = $orders->orderBy("billing_name", $sortDesc);
        break;
      case "billing_total":
        $orders = $orders->orderBy("billing_total", $sortDesc);
        break;
      case "billing_shipping_fee":
        $orders = $orders->orderBy("billing_shipping_fee", $sortDesc);
        break;
      case "billing_tax":
        $orders = $orders->orderBy("billing_tax", $sortDesc);
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
    $orders->setCollection($orders->getCollection()->makeHidden(['user_id']));
    return response()->json(new OrderCollection($orders));
  }

  public function show($id)
  {
    $order = Order::with(["products", "province:id,name", "district:id,name", "user"])
      ->findOrFail($id);
    return response()->json([
      'order' => $order
    ]);
  }

  public function destroy(Product $product)
  {
    $user = Auth::user();
    if (!$user || $user->cannot('order.delete')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $product->delete();
    return response()->noContent();
  }


  public function bulkDestroy(Request $request)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('order.delete')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    Order::whereIn('id', $request->listIds)->delete();
    return response()->noContent();
  }

  public function export(Request $request)
  {
    $type = $request->input('type', 'xlsx');
    return Excel::download(new OrdersExport, 'users.' . $type);
  }
}
