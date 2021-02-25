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
    $user = auth()->user();
    if (!$user || $user->cannot('brand.access')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $brands = Brand::where('name', 'LIKE', "%{$request->input('q')}%")
      ->select(['id', 'name', 'description'])
      ->withCount('products');
    $sortBy = $request->input("sortBy");
    $sortDesc = $request->input("sortDesc") == "false" ? "asc" : "desc";
    switch ($sortBy) {
      case "id":
        $brands = $brands->orderBy("id", $sortDesc);
        break;
      case "name":
        $brands = $brands->orderBy("name", $sortDesc);
        break;
      case "description":
        $brands = $brands->orderBy("description", $sortDesc);
        break;
      case "products_count":
        $brands = $brands->orderBy("products_count", $sortDesc);
        break;
      default:
        $brands = $brands->latest();
    }
    return response()->json(new BrandCollection($brands->paginate($request->input("per_page", 12))));
  }

  public function store(BrandRequest $request)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('brand.create')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
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
    $user = auth()->user();
    if (!$user || $user->cannot('brand.update')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    try {
      Brand::findOrFail($id)->update($request->validated());
      return response()->noContent();
    } catch (\Exception $exception) {
      return response()->json([
        "success" => false,
        "errors" => $exception->getMessage()
      ], 422);
    }
  }

  public function show($id, Request $request)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('brand.view')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    try {
      $brand = Brand::findOrFail($id);
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

  public function destroy(Brand $brand)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('brand.delete')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $brand->delete();
    return response()->noContent();
  }

  public function bulkDestroy(Request $request)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('brand.delete')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    Brand::whereIn('id', $request->listIds)->delete();
    return response()->noContent();
  }
}
