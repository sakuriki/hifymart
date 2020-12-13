<?php

namespace App\Models;

use App\Models\Province;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
  public function province()
  {
    return $this->belongsTo(Province::class);
  }

  public function orders()
  {
    return $this->hasMany(Order::class);
  }
}
