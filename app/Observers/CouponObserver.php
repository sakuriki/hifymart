<?php

namespace App\Observers;

use App\Models\Coupon;

class CouponObserver
{
  /**
   * Listen to the Coupon creating event.
   *
   * @param  \App\Models\Coupon  $coupon
   * @return void
   */
  public function creating(Coupon $coupon): void
  {
    if ($coupon->code) {
      return;
    }
    $code = new Coupon();
    $coupon->code = $code->generate();
  }
}
