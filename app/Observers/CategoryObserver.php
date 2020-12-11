<?php

namespace App\Observers;

use App\Models\Category;
use Illuminate\Support\Str;

class CategoryObserver
{
  /**
   * Listen to the Video saving event.
   *
   * @param  \App\Models\Category  $category
   * @return void
   */
  public function saving(Category $category): void
  {
    // produce a slug based on the video title
    $slug = Str::slug($category->name, '-');

    // check to see if any other slugs exist that are the same & count them
    $count = Category::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();

    // if other slugs exist that are the same, append the count to the slug
    $category->slug = $count ? "{$slug}-{$count}" : $slug;
  }
}
