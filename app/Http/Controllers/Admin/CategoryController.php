<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\Admin\CategoryCollection;

class CategoryController extends Controller
{
  public function index(Request $request)
  {
    $categories = Category::withCount('products')
      ->paginate($request->input("per_page", 12));
    return response()->json(new CategoryCollection($categories));
  }

  public function store(CategoryRequest $request)
  {
    try {
      $category = Category::create($request->validated());
      return response()->json([
        "category" => $category
      ]);
    } catch (\Exception $exception) {
      return response()->json([
        "success" => false,
        "errors" => $exception->getMessage()
      ], 422);
    }
  }

  public function update($id, CategoryRequest $request)
  {
    try {
      $category = Category::findOrFail($id)->update($request->validated());
      return response()->json([
        "category" => $category
      ]);
    } catch (\Exception $exception) {
      return response()->json([
        "success" => false,
        "errors" => $exception->getMessage()
      ], 422);
    }
  }

  public function delete(Category $category)
  {
    $category->delete();
    return response()->noContent();
  }
}
