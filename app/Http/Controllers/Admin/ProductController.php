<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Admin\ProductCollection;

class ProductController extends Controller
{
  public function index(Request $request)
  {
    $products = Product::with(["brand:id,name", "category:id,name"])
      ->withCount('orders')
      ->paginate($request->input("per_page", 12));
    return response()->json(new ProductCollection($products));
  }

  public function store(ProductRequest $request)
  {
    try {
      DB::beginTransaction();
      if (!$request->file("featured_image")) {
        return response()->json([
          "success" => false,
          "errors" => [
            "featured_image" => "Ảnh đại diện không được trống"
          ]
        ], 422);
      }
      $filename = pathinfo($request->file('featured_image')->getClientOriginalName(), PATHINFO_FILENAME);
      $extension = $request->file("featured_image")->extension();
      $final_name = $filename . "_" . date('mdYHis') . "_" . uniqid() . "." . $extension;
      $filePath = $request->file("featured_image")->storeAs(
        '/public/featured_image',
        $final_name
      );
      $url = Storage::url($filePath);
      $product = Product::create(
        array_merge(
          $request->validated(),
          ["featured_image" => $url]
        )
      );
      DB::commit();
      return response()->json([
        "product" => $product
      ]);
    } catch (\Exception $exception) {
      DB::rollBack();
      return response()->json([
        "success" => false,
        "errors" => $exception->getMessage()
      ], 422);
    }
  }

  public function update($id, ProductRequest $request)
  {
    try {
      DB::beginTransaction();
      $data = $request->validated();
      if ($request->file('featured_image')) {
        $filename = pathinfo($request->file('featured_image')->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $request->file("featured_image")->extension();
        $final_name = $filename . "_" . date('mdYHis') . "_" . uniqid() . "." . $extension;
        $filePath = $request->file("featured_image")->storeAs(
          '/public/featured_image',
          $final_name
        );
        $url = Storage::url($filePath);
        $data = array_merge(
          $request->validated(),
          ["featured_image" => $url]
        );
      }
      $product = Product::findOrFail($id)->update($data);
      DB::commit();
      return response()->json([
        "product" => $product
      ]);
    } catch (\Exception $exception) {
      DB::rollBack();
      return response()->json([
        "success" => false,
        "errors" => $exception->getMessage()
      ], 422);
    }
  }

  public function delete(Product $product)
  {
    $product->delete();
    return response()->noContent();
  }
}
