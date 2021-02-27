<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Str;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Admin\ProductImageCollection;

class ProductImageController extends Controller
{
  public function index(Request $request)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('product.access')) {
      return response()->json([
        'code' => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $productImages = ProductImage::select(['id', 'url', 'product_id'])
      ->paginate($request->input('per_page', 10));
    return response()->json(new ProductImageCollection($productImages));
  }

  public function destroy($id)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('product.delete')) {
      return response()->json([
        'code' => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $image = ProductImage::findOrFail($id);
    Storage::delete(Str::replaceFirst("storage", "public", $image->url));
    $image->delete();
    return response()->noContent();
  }
}
