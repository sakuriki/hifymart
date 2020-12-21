<?php

namespace App\Models;

use App\Concern\UploadAble;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
  use UploadAble;

  protected $fillable = [
    'url', 'product_id'
  ];

  protected $casts = [
    'product_id' => 'integer',
  ];

  public function product()
  {
    return $this->belongsTo(Product::class);
  }
}
