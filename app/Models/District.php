<?php

namespace App\Models;

use App\Models\District;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
  public function districts()
  {
    return $this->hasMany(District::class);
  }
}
