<?php

namespace App\Http\Resources\Admin;

use RedisManager;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollection extends ResourceCollection
{
  /**
   * Transform the resource collection into an array.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return array
   */
  public function toArray($request)
  {
    return [
      // dùng tạm, đợi PHPRedis update ZMSCORE trong Redis 6.2
      'products' => $this->collection->transform(function ($item) {
        $item->views_count = RedisManager::ZSCORE("products_visits", $item->id) ?: 0;
        return $item;
      }),
      'pagination' => [
        'total' => $this->total(),
        'count' => $this->count(),
        'per_page' => $this->perPage(),
        'current_page' => $this->currentPage(),
        'total_pages' => $this->lastPage()
      ]
    ];
  }
}
