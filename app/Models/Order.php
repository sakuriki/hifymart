<?php

namespace App\Models;

use App\Models\Product;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
  protected $fillable = [
    'status'
  ];

  public function customer()
  {
    return $this->belongsTo(Customer::class);
  }

  public function products()
  {
    return $this->belongsToMany(Product::class);
  }
}
