<?php

namespace App\Observers;

use App\Models\Brand;
use Illuminate\Support\Str;

class BrandObserver
{
  /**
   * Listen to the Video creating event.
   *
   * @param  \App\Models\Brand  $brand
   * @return void
   */
  public function creating(Brand $brand): void
  {
    if ($brand->slug) {
      return;
    }
    $slug = Str::slug($brand->name, '-');
    $count = Brand::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();
    $brand->slug = $count ? "{$slug}-{$count}" : $slug;
  }
}
