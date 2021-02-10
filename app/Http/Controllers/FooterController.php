<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;

class FooterController extends Controller
{
  public function __invoke(Request $request)
  {
    $brands = Brand::select(['id', 'name', 'slug'])
      ->latest()
      ->take(5)
      ->get();
    $categories = Category::select(['id', 'name', 'slug'])
      ->latest()
      ->take(5)
      ->get();
    return response()->json([
      'brands' => $brands,
      'categories' => $categories
    ]);
  }
}
