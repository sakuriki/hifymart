<?php

namespace App\Models;

use App\Models\User;
use App\Models\Product;
use App\Models\District;
use App\Models\Province;
use App\Models\OrderProduct;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
  protected $fillable = [
    'status', 'user_id', 'province_id', 'district_id', 'billing_email', 'billing_name', 'billing_address', 'billing_note', 'billing_phone', 'billing_discount', 'billing_discount_code', 'billing_subtotal', 'billing_tax', 'billing_shipping_fee', 'billing_total', 'payment_type', 'is_paid', 'shipped', 'error'
  ];

  protected $casts = [
    "shipped" => "boolean",
    "is_paid" => "boolean",
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function products()
  {
    return $this->belongsToMany(Product::class)->withPivot('quantity', 'price');
  }

  public function order_product()
  {
    return $this->hasMany(OrderProduct::class, "order_id");
  }

  public function province()
  {
    return $this->belongsTo(Province::class);
  }

  public function district()
  {
    return $this->belongsTo(District::class);
  }
}
