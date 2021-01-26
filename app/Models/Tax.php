<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
  protected $fillable = [
    'name',
    'value'
  ];

  public function products()
  {
    return $this->hasMany(Product::class);
  }
}
