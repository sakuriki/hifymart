<?php

namespace App\Observers;

use App\Models\Order;
use App\Mail\SendInvoice;
use Illuminate\Support\Facades\Mail;

class OrderObserver
{
  /**
   * Listen to the Order created event.
   *
   * @param  \App\Models\Order  $order
   * @return void
   */
  public function created(Order $order): void
  {
    Mail::to($order->billing_email)->queue(new SendInvoice($order));
  }
}
