<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CouponController extends Controller
{
  public function __invoke($code, Request $request)
  {
    $coupon = Coupon::select(['code', 'value', 'number', 'is_percent', 'min', 'max', 'starts_at', 'expires_at'])
      ->where('code', $code)
      ->first();
    if (!$coupon || !$coupon->isStarted()) {
      return response()->json([
        'errors' => [
          'coupon' => ['Coupon không có thực']
        ]
      ], 404);
    }
    if ($coupon->isExpired()) {
      return response()->json([
        'errors' => [
          'coupon' => ['Coupon đã hết hạn']
        ]
      ], 404);
    }
    if ($coupon->isOutOfUse()) {
      return response()->json([
        'errors' => [
          'coupon' => ['Coupon đã hết số lần sử dụng']
        ]
      ], 404);
    }
    return response()->json([
      'coupon' => $coupon->makeHidden(['number', 'starts_at', 'expires_at'])
    ]);
  }
}
