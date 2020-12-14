<?php

namespace App\Http\Resources\Admin;

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
      'products' => $this->collection->transform(function ($page) {
        return [
          'id' => $page->id,
          'name' => $page->name,
          'slug' => $page->slug,
          'description' => $page->description,
          'price' => $page->price,
          'quantity' => $page->quantity,
          'featured_image' => $page->featured_image,
          'brand' => $page->brand,
          'category' => $page->category,
          'orders_count' => $page->orders_count
        ];
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
