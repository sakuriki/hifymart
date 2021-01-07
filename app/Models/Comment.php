<?php

namespace App\Models;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Comment extends Model
{
  protected $fillable = [
    'comment',
    'user_id',
    'product_id',
    'parent_id'
  ];

  public function product()
  {
    return $this->belongsTo(Product::class);
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function replies()
  {
    return $this->hasMany(Comment::class, 'parent_id')->with('user:id,name');
  }

  public function scopeSearch(Builder $query, ?string $search)
  {
    if ($search) {
      return $query->where('comments', 'LIKE', "%{$search}%");
    }
  }
}
