<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
  protected $fillable = [
    'name',
    'parent_id',
    'description'
  ];

  public function products()
  {
    return $this->hasMany(Product::class);
  }
}
