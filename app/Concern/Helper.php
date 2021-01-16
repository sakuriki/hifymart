<?php

namespace App\Concern;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder;

trait Helper
{
  public function scopeSearch(Builder $query, ?string $search, $from)
  {
    if ($search && $from) {
      foreach (Arr::wrap($from) as $key) {
        $query = $query->where($key, 'LIKE', "%{$search}%");
      }
      return $query;
    }
  }

  public function scopeLatest(Builder $query, ?string $order = "desc"): Builder
  {
    return $query->orderBy('created_at', $order);
  }
}
