<?php

namespace App\Models;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
  protected $fillable = ['approved', 'user_id', 'rating', 'review'];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function product()
  {
    return $this->belongsTo(Product::class);
  }

  public function comments()
  {
    return $this->morphMany(Comment::class, 'commentable')->whereNull('parent_id');
  }
}
