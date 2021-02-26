<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
  public function index(Request $request)
  {
    $brands = Brand::select(['id', 'name', 'slug'])->get();
    return response()->json([
      'brands' => $brands
    ]);
  }

  public function show($slug, Request $request)
  {
    $brand = Brand::where('slug', $slug)
      ->select('id', 'name', 'description')
      ->firstOrFail();
    return response()->json([
      'brand' => $brand
    ]);
  }
}
