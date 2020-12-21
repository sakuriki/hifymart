<?php

namespace App\Observers;

use App\Models\Tag;
use Illuminate\Support\Str;

class TagObserver
{
  /**
   * Listen to the Video creating event.
   *
   * @param  \App\Models\Tag  $tag
   * @return void
   */
  public function creating(Tag $tag): void
  {
    if ($tag->slug) {
      return;
    }
    $slug = Str::slug($tag->name, '-');
    $count = Tag::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();
    $tag->slug = $count ? "{$slug}-{$count}" : $slug;
  }
}
