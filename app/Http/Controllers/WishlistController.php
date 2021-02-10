<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\WishlistCollection;

class WishlistController extends Controller
{
  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    try {
      Wishlist::firstOrCreate([
        'product_id' => $request->product_id,
        'user_id' => auth()->user()->id
      ]);
      return response()->noContent();
    } catch (\Exception $exception) {
      return response()->json([
        "success" => false,
        "errors" => $exception->getMessage()
      ], 422);
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Wishlist  $wishlist
   * @return \Illuminate\Http\Response
   */
  public function show($user_id, Request $request)
  {
    // return auth()->user()
    //   // ->load('wishlists.product');
    //   ->load(['wishlists.product' => function ($q) {
    //     $q->orderBy("price", 'desc');
    //   }]);
    // return $user = auth()->user();
    $data = Wishlist::where("user_id", $user_id)
      ->join('products', 'wishlists.product_id', '=', 'products.id');
    $sortBy = $request->input("sortBy");
    $sortDesc = $request->input("sortDesc") == "false" ? "asc" : "desc";
    switch ($sortBy) {
      case "name":
        $data = $data->orderBy("name", $sortDesc);
        break;
      case "price":
        $data = $data->orderBy("price", $sortDesc)
          ->orderBy("sale_off_price", $sortDesc);
        break;
      default:
        $data = $data->latest();
    }
    return response()->json(new WishlistCollection($data->paginate($request->input('per_page', 10))));
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Wishlist  $wishlist
   * @return \Illuminate\Http\Response
   */
  public function destroy($product_id)
  {
    try {
      Wishlist::where('product_id', $product_id)->first()->delete();
    } catch (\Exception $exception) {
      return response()->json([
        'success' => false,
        'msg' => $exception->getMessage()
      ], 422);
    };
    return response()->noContent();
  }
}
