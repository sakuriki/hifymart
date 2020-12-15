<?php

namespace App\Http\Resources;

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
          'sale_off_price' => $page->sale_off_price,
          'sale_off_percent' => $page->sale_off_percent,
          'sale_off_quantity' => $page->sale_off_quantity,
          'featured_image' => $page->featured_image,
          'brand_id' => $page->brand_id,
          'category_id' => $page->category_id
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
