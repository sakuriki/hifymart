<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Comment extends Model
{

  protected $hidden = ['commentable_id', 'commentable_type'];
  protected $fillable = [
    'comment',
    'user_id',
    'parent_id',
    'is_pending'
  ];
  protected $casts = [
    'is_pending' => 'boolean'
  ];

  public function commentable()
  {
    return $this->morphTo();
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function replies()
  {
    return $this->hasMany(Comment::class, 'parent_id');
  }

  public function scopeSearch(Builder $query, ?string $search)
  {
    if ($search) {
      return $query->where('comments', 'LIKE', "%{$search}%");
    }
  }
}
