<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Str;

class ProductObserver
{
  /**
   * Listen to the Video creating event.
   *
   * @param  \App\Models\Product  $product
   * @return void
   */
  public function creating(Product $product): void
  {
    if ($product->slug) {
      return;
    }
    $slug = Str::slug($product->name, '-');
    $count = Product::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();
    $product->slug = $count ? "{$slug}-{$count}" : $slug;
  }
}
