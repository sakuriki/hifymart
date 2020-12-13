<?php

namespace App\Models;

use App\Models\Order;
use App\Models\District;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
  public function districts()
  {
    return $this->hasMany(District::class);
  }

  public function orders()
  {
    return $this->hasMany(Order::class);
  }
}
