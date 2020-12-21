<?php

namespace App\Observers;

use App\Models\Category;
use Illuminate\Support\Str;

class CategoryObserver
{
  /**
   * Listen to the Video creating event.
   *
   * @param  \App\Models\Category  $category
   * @return void
   */
  public function creating(Category $category): void
  {
    if ($category->slug) {
      return;
    }
    $slug = Str::slug($category->name, '-');
    $count = Category::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();
    $category->slug = $count ? "{$slug}-{$count}" : $slug;
  }
}
