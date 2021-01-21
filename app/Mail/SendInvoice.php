<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendInvoice extends Mailable implements ShouldQueue
{
  use Queueable, SerializesModels;

  protected $order;

  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct($order)
  {
    $this->order = $order;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    return $this->from(env('MAIL_FROM_ADDRESS', 'orders@vietshop.com'), env('MAIL_FROM_NAME', 'VietShop'))
      ->subject(env('APP_NAME', 'VietShop') . ' - Đơn hàng mới ' . $this->order->id)
      ->view('emails.sendinvoice')
      ->with([
        'order' => $this->order
      ]);
  }
}
