<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Coupon
 *
 * @property int $id
 * @property string $code
 * @property int $value
 * @property int|null $number
 * @property int|null $min
 * @property int|null $max
 * @property bool $is_percent
 * @property string|null $starts_at
 * @property string|null $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder|Coupon newModelQuery()
 * @method static Builder|Coupon newQuery()
 * @method static Builder|Coupon query()
 * @method static Builder|Coupon redeemable($now = null, $total = null)
 * @method static Builder|Coupon whereCode($value)
 * @method static Builder|Coupon whereCreatedAt($value)
 * @method static Builder|Coupon whereExpiresAt($value)
 * @method static Builder|Coupon whereId($value)
 * @method static Builder|Coupon whereIsPercent($value)
 * @method static Builder|Coupon whereMax($value)
 * @method static Builder|Coupon whereMin($value)
 * @method static Builder|Coupon whereNumber($value)
 * @method static Builder|Coupon whereStartsAt($value)
 * @method static Builder|Coupon whereUpdatedAt($value)
 * @method static Builder|Coupon whereValue($value)
 * @mixin \Eloquent
 */
class Coupon extends Model
{
  protected $fillable = [
    'code',
    'value',
    'number',
    'min',
    'max',
    'is_percent',
    'starts_at',
    'expires_at',
  ];

  protected $casts = [
    'value' => 'integer',
    'number' => 'integer',
    'min' => 'integer',
    'max' => 'integer',
    'is_percent' => 'boolean'
  ];

  public function generate($length = 12)
  {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
    }
    return $randomString;
  }

  public function isExpired()
  {
    return $this->expires_at ? Carbon::now()->gte($this->expires_at) : false;
  }

  public function isStarted()
  {
    return $this->starts_at ? Carbon::now()->gte($this->starts_at) : true;
  }

  public function isOutOfUse()
  {
    return $this->number <= 0;
  }

  public function isUseable($total = 0)
  {
    return (!$this->min || $total >= $this->min);
  }

  public function isRedeemable($total = 0)
  {
    if (!$this->isOutOfUse() && !$this->isExpired() && $this->isStarted() && $this->isUseable($total)) {
      return true;
    }
    return false;
  }

  public function scopeRedeemable(Builder $query, $now = null, $total = null)
  {
    $now = $now ?? Carbon::now()->toDateTimeString();
    return $query->where('starts_at', '<=', $now)
      ->where('expires_at', '>', $now)
      ->where(function ($query) {
        $query->where('number', '>', 0)
          ->orWhereNull('number');
      })
      ->when($total, function ($query, $total) {
        return $query->where(function ($query) use ($total) {
          $query->where('min', '<=', $total)
            ->orWhereNull('min');
        });
      });
  }
}
