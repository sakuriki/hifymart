<?php

namespace App\Models;

use App\Models\Order;
use App\Concern\Helper;
use App\Models\Product;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Wishlist;
use App\Models\AddressBook;
use App\Concern\HasPermissionsTrait;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
  use Notifiable, HasPermissionsTrait, Helper;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name', 'email', 'password', 'phone'
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password', 'remember_token',
  ];

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
  ];

  /**
   * Get the identifier that will be stored in the subject claim of the JWT.
   *
   * @return mixed
   */
  public function getJWTIdentifier()
  {
    return $this->getKey();
  }

  /**
   * Return a key value array, containing any custom claims to be added to the JWT.
   *
   * @return array
   */
  public function getJWTCustomClaims()
  {
    return [];
  }

  public function comments()
  {
    return $this->hasMany(Comment::class);
  }

  public function wishlists()
  {
    return $this->hasMany(Wishlist::class);
  }

  public function orders()
  {
    return $this->hasMany(Order::class);
  }

  public function addressBooks()
  {
    return $this->hasMany(AddressBook::class);
  }

  public function products()
  {
    return Product::join('order_product', 'products.id', '=', 'order_product.product_id')
      ->join('orders', 'order_product.order_id', '=', 'orders.id')
      ->join('users', 'orders.user_id', '=', 'users.id')
      ->where('users.id', $this->id);
  }
}
