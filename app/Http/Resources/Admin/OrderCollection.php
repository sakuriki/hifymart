<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderCollection extends ResourceCollection
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
      'orders' => $this->collection->transform(function ($page) {
        return [
          'id' => $page->id,
          'billing_email' => $page->billing_email,
          'billing_name' => $page->billing_name,
          'billing_address' => $page->billing_address,
          'billing_district' => $page->district,
          'billing_province' => $page->province,
          'billing_note' => $page->billing_note,
          'billing_phone' => $page->billing_phone,
          'billing_discount' => $page->billing_discount,
          'billing_discount_code' => $page->billing_discount_code,
          'billing_subtotal' => $page->billing_subtotal,
          'billing_tax' => $page->billing_tax,
          'billing_total' => $page->billing_total,
          'shipped' => $page->shipped,
          'error' => $page->error,
          'order_product' => $page->order_product,
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
