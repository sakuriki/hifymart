<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
  public function index(Request $request)
  {
    $brands = Brand::select(['id', 'name'])->get();
    return response()->json([
      'brands' => $brands
    ]);
  }
}
