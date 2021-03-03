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

/**
 * App\Models\Product
 *
 * @property int $id
 * @property int|null $brand_id
 * @property int|null $category_id
 * @property int|null $tax_id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property int $price
 * @property int|null $sale_off_price
 * @property int|null $sale_off_quantity
 * @property string|null $sale_off_start
 * @property string|null $sale_off_end
 * @property int $quantity
 * @property string $featured_image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Brand|null $brand
 * @property-read Category|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection|Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read mixed $tag_names
 * @property-read \Illuminate\Database\Eloquent\Collection|ProductImage[] $images
 * @property-read int|null $images_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Order[] $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Rating[] $ratings
 * @property-read int|null $ratings_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Rating[] $ratingsWithUser
 * @property-read int|null $ratings_with_user_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Tag[] $tags
 * @property-read int|null $tags_count
 * @property-read User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|Wishlist[] $wishlists
 * @property-read int|null $wishlists_count
 * @method static Builder|Product latest($order = 'desc')
 * @method static Builder|Product newModelQuery()
 * @method static Builder|Product newQuery()
 * @method static Builder|Product onSale()
 * @method static Builder|Product query()
 * @method static Builder|Product search($search, $from)
 * @method static Builder|Product whereBrandId($value)
 * @method static Builder|Product whereCategoryId($value)
 * @method static Builder|Product whereCreatedAt($value)
 * @method static Builder|Product whereDescription($value)
 * @method static Builder|Product whereFeaturedImage($value)
 * @method static Builder|Product whereId($value)
 * @method static Builder|Product whereName($value)
 * @method static Builder|Product wherePrice($value)
 * @method static Builder|Product whereQuantity($value)
 * @method static Builder|Product whereSaleOffEnd($value)
 * @method static Builder|Product whereSaleOffPrice($value)
 * @method static Builder|Product whereSaleOffQuantity($value)
 * @method static Builder|Product whereSaleOffStart($value)
 * @method static Builder|Product whereSlug($value)
 * @method static Builder|Product whereTaxId($value)
 * @method static Builder|Product whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string|null $content
 * @property-read \App\Models\Tax|null $tax
 * @method static Builder|Product whereContent($value)
 */
class Product extends Model
{
  use Helper;

  protected $fillable = [
    'name',
    'brand_id',
    'category_id',
    'description',
    'content',
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

  public function tax()
  {
    return $this->belongsTo(Tax::class);
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
