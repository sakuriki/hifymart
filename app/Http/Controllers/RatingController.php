<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Rating;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\RatingCollection;

class RatingController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    //
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Product::id  $id
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function show($id, Request $request)
  {
    $orders = Order::select(['orders.id', 'orders.is_paid', 'orders.user_id'])
      ->whereHas('products', function ($query) use ($id) {
        $query->where('id', $id);
      })
      ->whereNotNull('orders.user_id')
      ->where('orders.is_paid', 1)
      ->get()
      ->keyBy('user_id');
    // $orders = Order::select(['orders.id', 'orders.is_paid', 'orders.user_id'])
    //   ->join('order_product', 'orders.id', '=', 'order_product.order_id')
    //   ->where('order_product.product_id', $id)
    //   ->whereNotNull('orders.user_id')
    //   ->where('orders.is_paid', 1)
    //   ->get()
    //   ->keyBy('user_id');
    $query_ratings = Rating::where('product_id', $id)
      ->where('approved', 1)
      ->with(['user:id,name'])
      ->select(['id', 'rating', 'review', 'user_id', 'product_id', 'created_at'])
      ->latest();
    $total_rating = $query_ratings->count();
    $ratings = $query_ratings
      ->offset($request->input('offset', 0))
      ->limit($request->input('page_size', 4))
      ->get();
    $ratings->transform(function ($item) use ($orders) {
      $item->is_purchased = $orders[$item->user->id]->is_paid ?? 0;
      return $item;
    });
    return response()->json([
      'ratings' => $ratings,
      'total' => $total_rating
    ]);
    // return response()->json(new RatingCollection($ratings));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Rating  $rating
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Rating $rating)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Rating  $rating
   * @return \Illuminate\Http\Response
   */
  public function destroy(Rating $rating)
  {
    //
  }
}
