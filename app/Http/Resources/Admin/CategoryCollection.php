<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CategoryCollection extends ResourceCollection
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
      'categories' => $this->collection->transform(function ($page) {
        return [
          'id' => $page->id,
          'name' => $page->name,
          'slug' => $page->slug,
          'description' => $page->description,
          'products_count' => $page->products_count,
          'created_at' => $page->created_at,
          'updated_at' => $page->updated_at
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
