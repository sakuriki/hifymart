<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Product extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return array
   */
  public function toArray($request)
  {
    return [
      'id' => $this->id,
      'name' => $this->name,
      'slug' => $this->slug,
      'price' => $this->price,
      'sale_off_price' => $this->sale_off_price,
      'ratings_average' => (float) $this->ratings_average,
      'featured_image' => $this->featured_image,
    ];
  }
}
