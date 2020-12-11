<?php

namespace App\Http\Controllers\Admin;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Http\Requests\BrandRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\BrandCollection;

class Brandcontroller extends Controller
{
  public function index(Request $request)
  {
    $brands = Brand::withCount('products')
      ->paginate($request->input("per_page", 12));
    return response()->json(new BrandCollection($brands));
  }

  public function store(BrandRequest $request)
  {
    try {
      $brand = Brand::create($request->validated());
      return response()->json([
        "brand" => $brand
      ]);
    } catch (\Exception $exception) {
      return response()->json([
        "success" => false,
        "errors" => $exception->getMessage()
      ], 422);
    }
  }

  public function update($id, BrandRequest $request)
  {
    try {
      $brand = Brand::findOrFail($id)->update($request->validated());
      return response()->json([
        "brand" => $brand
      ]);
    } catch (\Exception $exception) {
      return response()->json([
        "success" => false,
        "errors" => $exception->getMessage()
      ], 422);
    }
  }

  public function delete(Brand $brand)
  {
    $brand->delete();
    return response()->noContent();
  }
}
