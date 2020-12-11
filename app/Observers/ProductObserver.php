<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Str;

class ProductObserver
{
  /**
   * Listen to the Video saving event.
   *
   * @param  \App\Models\Product  $product
   * @return void
   */
  public function saving(Product $product): void
  {
    // produce a slug based on the video title
    $slug = Str::slug($product->title, '-');

    // check to see if any other slugs exist that are the same & count them
    $count = Product::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();

    // if other slugs exist that are the same, append the count to the slug
    $product->slug = $count ? "{$slug}-{$count}" : $slug;
  }
}
