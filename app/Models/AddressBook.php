<?php

namespace App\Models;

use App\Models\User;
use App\Models\District;
use App\Models\Province;
use Illuminate\Database\Eloquent\Model;

class AddressBook extends Model
{
  protected $fillable = [
    'user_id', 'province_id', 'district_id', 'email', 'name', 'address', 'phone',
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
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
