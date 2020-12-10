<?php

namespace App\Models;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
  protected $fillable = [
    'name',
    'address',
    'phone_number'
  ];

  public function orders()
  {
    return $this->hasMany(Order::class);
  }
}
