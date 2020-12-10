<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
  protected $fillable = [
    'title',
    'description'
  ];

  public function products()
  {
    return $this->hasMany(Product::class);
  }
}
