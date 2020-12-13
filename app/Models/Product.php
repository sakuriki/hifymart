<?php

namespace App\Models;

use App\Models\Tag;
use App\Models\User;
use App\Models\Brand;
use App\Models\Order;
use App\Concern\Helper;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  use Helper;

  protected $fillable = [
    'title',
    'brand_id',
    'category_id',
    'description',
    'price',
    'quantity',
    'featured_image'
  ];

  protected $hidden = [
    'pivot'
  ];

  public function brand()
  {
    return $this->belongsTo(Brand::class);
  }

  public function category()
  {
    return $this->belongsTo(Category::class);
  }

  public function tags()
  {
    return $this->belongsToMany(Tag::class);
  }

  public function orders()
  {
    return $this->belongsToMany(Order::class);
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
