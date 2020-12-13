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
    $slug = Str::slug($product->title, '-');
    $count = Product::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();
    $product->slug = $count ? "{$slug}-{$count}" : $slug;
  }
}
