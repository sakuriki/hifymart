<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
  public function index(Request $request)
  {
    $categories = Category::select(['id', 'name'])->get();
    return response()->json([
      'categories' => $categories
    ]);
  }
}
