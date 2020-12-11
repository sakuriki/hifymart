<?php

namespace App\Observers;

use App\Models\Brand;
use Illuminate\Support\Str;

class BrandObserver
{
  /**
   * Listen to the Video saving event.
   *
   * @param  \App\Models\Brand  $brand
   * @return void
   */
  public function saving(Brand $brand): void
  {
    // produce a slug based on the video title
    $slug = Str::slug($brand->name, '-');

    // check to see if any other slugs exist that are the same & count them
    $count = Brand::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();

    // if other slugs exist that are the same, append the count to the slug
    $brand->slug = $count ? "{$slug}-{$count}" : $slug;
  }
}
