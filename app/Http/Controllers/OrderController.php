<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
  public function show($id)
  {
    $order = Order::with(['products', 'district', 'province'])
      ->findOrFail($id);
    return response()->json([
      'order' => $order
    ]);
  }
}
