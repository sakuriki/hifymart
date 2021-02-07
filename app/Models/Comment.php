<?php

namespace App\Models;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Comment
 *
 * @property int $id
 * @property int|null $parent_id
 * @property string|null $name
 * @property string|null $phone
 * @property string|null $email
 * @property string $content
 * @property int|null $user_id
 * @property int $product_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Product $product
 * @property-read \Illuminate\Database\Eloquent\Collection|Comment[] $replies
 * @property-read int|null $replies_count
 * @property-read User|null $user
 * @method static Builder|Comment newModelQuery()
 * @method static Builder|Comment newQuery()
 * @method static Builder|Comment query()
 * @method static Builder|Comment search($search)
 * @method static Builder|Comment whereContent($value)
 * @method static Builder|Comment whereCreatedAt($value)
 * @method static Builder|Comment whereEmail($value)
 * @method static Builder|Comment whereId($value)
 * @method static Builder|Comment whereName($value)
 * @method static Builder|Comment whereParentId($value)
 * @method static Builder|Comment wherePhone($value)
 * @method static Builder|Comment whereProductId($value)
 * @method static Builder|Comment whereUpdatedAt($value)
 * @method static Builder|Comment whereUserId($value)
 * @mixin \Eloquent
 */
class Comment extends Model
{
  protected $fillable = [
    'name',
    'phone',
    'email',
    'content',
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
      return $query->where('content', 'LIKE', "%{$search}%")
        ->orWhere('name', 'LIKE', "%{$search}%");
    }
  }
}
