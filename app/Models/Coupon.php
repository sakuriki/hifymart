<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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

  public function isRedeemable()
  {
    if (!$this->isOutOfUse() && !$this->isExpired() && $this->isStarted()) {
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
