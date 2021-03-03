<?php

namespace App\Http\Controllers\Admin;

use App\Models\Coupon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CouponRequest;
use App\Http\Resources\Admin\CouponCollection;

class CouponController extends Controller
{
  public function index(Request $request)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('coupon.access')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $coupons = Coupon::where('code', 'LIKE', "%{$request->input('q')}%")
      ->select(['id', 'code', 'value', 'number', 'is_percent', 'starts_at', 'expires_at']);
    // ->withCount('orders');
    $sortBy = $request->input("sortBy");
    $sortDesc = $request->input("sortDesc") == "false" ? "asc" : "desc";
    switch ($sortBy) {
      case "id":
        $coupons = $coupons->orderBy("id", $sortDesc);
        break;
      case "code":
        $coupons = $coupons->orderBy("code", $sortDesc);
        break;
      case "value":
        $coupons = $coupons->orderBy("value", $sortDesc);
        break;
      case "number":
        $coupons = $coupons->orderBy("number", $sortDesc);
        break;
      case "is_percent":
        $coupons = $coupons->orderBy("is_percent", $sortDesc);
        break;
      case "starts_at":
        $coupons = $coupons->orderBy("starts_at", $sortDesc);
        break;
      case "expires_at":
        $coupons = $coupons->orderBy("expires_at", $sortDesc);
        break;
        // case "orders_count":
        //   $coupons = $coupons->orderBy("orders_count", $sortDesc);
        //   break;
      default:
        $coupons = $coupons->latest();
    }
    return response()->json(new CouponCollection($coupons->paginate($request->input("per_page", 12))));
  }

  public function store(CouponRequest $request)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('coupon.create')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    try {
      $coupon = Coupon::create($request->validated());
      return response()->json([
        "coupon" => $coupon
      ]);
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

  public function update($id, CouponRequest $request)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('coupon.update')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    try {
      Coupon::findOrFail($id)->update($request->validated());
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

  public function show($id, Request $request)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('coupon.view')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    try {
      $coupon = Coupon::findOrFail($id);
      return response()->json([
        'coupon' => $coupon
      ]);
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

  public function destroy(Coupon $coupon)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('coupon.delete')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $coupon->delete();
    return response()->noContent();
  }

  public function bulkDestroy(Request $request)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('coupon.delete')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    Coupon::whereIn('id', $request->listIds)->delete();
    return response()->noContent();
  }
}
