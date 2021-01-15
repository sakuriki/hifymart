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

  public function parent()
  {
    return $this->belongsTo(static::class, 'parent_id');
  }

  public function children()
  {
    return $this->hasMany(static::class, 'parent_id');
  }

  public function childrenRecursive()
  {
    return $this->children()->with('childrenRecursive:id,parent_id,name,slug');
  }

  public function products()
  {
    return $this->hasMany(Product::class);
  }
}
