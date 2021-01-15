<?php

namespace App\Models;

use App\Concern\Helper;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
  use Helper;

  protected $fillable = [
    'name'
  ];

  protected $hidden = [
    'pivot'
  ];

  public function products()
  {
    return $this->belongsToMany(Product::class);
  }
}
