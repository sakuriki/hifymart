<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\WishlistCollection;

class WishlistController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @param  \App\Wishlist  $wishlist
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    // return auth()->user()
    //   // ->load('wishlists.product');
    //   ->load(['wishlists.product' => function ($q) {
    //     $q->orderBy("price", 'desc');
    //   }]);
    // return $user = auth()->user();
    $user = Auth::user();
    // đã chặn ngoài middleware nhưng kiểm tra lại user cho chắc
    if (!$user) {
      return response()->json([
        'code' => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $data = Wishlist::where("user_id", $user->id)
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
        'errors' => [
          'error' => [
            $exception->getMessage()
          ]
        ]
      ], 422);
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Wishlist  $wishlist
   * @return \Illuminate\Http\Response
   */
  public function destroy($product_id)
  {
    $user = Auth::user();
    if (!$user) {
      return response()->json([
        'code' => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    Wishlist::where('product_id', $product_id)
      ->where('user_id', $user->id)
      ->delete();
    return response()->noContent();
  }
}
