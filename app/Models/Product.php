<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Tag;
use App\Models\User;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Rating;
use App\Concern\Helper;
use App\Models\Comment;
use App\Models\Category;
use App\Models\Wishlist;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
  use Helper;

  protected $fillable = [
    'name',
    'brand_id',
    'category_id',
    'description',
    'price',
    'sale_off_price',
    'sale_off_start',
    'sale_off_end',
    'sale_off_quantity',
    'quantity',
    'featured_image'
  ];

  // protected $hidden = [
  //   'pivot'
  // ];

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

  public function getTagNamesAttribute()
  {
    return $this->tags->pluck('name');
  }

  public function orders()
  {
    return $this->belongsToMany(Order::class);
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function wishlists()
  {
    return $this->hasMany(Wishlist::class);
  }

  public function ratings()
  {
    return $this->hasMany(Rating::class);
  }

  public function ratingsWithUser()
  {
    return $this->hasMany(Rating::class)->with('user:id,name');
  }

  public function images()
  {
    return $this->hasMany(ProductImage::class);
  }

  public function comments()
  {
    return $this->hasMany(Comment::class)->whereNull('parent_id');
  }

  public function averageRating($onlyApproved = false)
  {
    return $this->ratings()
      ->selectRaw('ROUND(AVG(rating)/0.5, 0)*0.5 as averageRating')
      ->when($onlyApproved, function ($query) {
        $query->where('approved', 1);
      })
      ->pluck('averageRating');
  }

  public function scopeonSale(Builder $query)
  {
    $now = Carbon::now()->toDateTimeString();
    return $query->whereNotNull('sale_off_price')
      ->where('sale_off_start', '<', $now)
      ->where('sale_off_end', '>', $now)
      ->where(function ($query) {
        $query->where('sale_off_quantity', '>', 0)
          ->orWhereNull('sale_off_quantity');
      });
  }

  public function getSalePrice($now = null)
  {
    $now = $now ?? Carbon::now()->toDateTimeString();
    if (
      ($this->sale_off_quantity && $this->sale_off_quantity <= 0)
      || $now < $this->sale_off_start
      || $now > $this->sale_off_end
      || !$this->sale_off_price
    ) {
      return $this->price;
    }
    return $this->sale_off_price;
  }
}
