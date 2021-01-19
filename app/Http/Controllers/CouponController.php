<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CouponController extends Controller
{
  public function __invoke($code, Request $request)
  {
    $coupon = Coupon::select(['id', 'code', 'value', 'is_percent', 'min', 'max', 'starts_at', 'expires_at'])
      ->where('code', $code)
      ->first();
    if (!$coupon) {
      return response()->json([
        'errors' => [
          'coupon' => ['Coupon không có thực']
        ]
      ], 404);
    }
    return response()->json([
      'coupon' => $coupon
    ]);
  }
}
