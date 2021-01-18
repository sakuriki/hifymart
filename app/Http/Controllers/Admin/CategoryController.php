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
    $user = auth()->user();
    if (!$user || $user->cannot('category.access')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $categories = Category::where('name', 'LIKE', "%{$request->input('q')}%")
      ->select(['id', 'name', 'description'])
      ->withCount('products');
    $sortBy = $request->input("sortBy");
    $sortDesc = $request->input("sortDesc") == "false" ? "asc" : "desc";
    switch ($sortBy) {
      case "id":
        $categories = $categories->orderBy("id", $sortDesc);
        break;
      case "name":
        $categories = $categories->orderBy("name", $sortDesc);
        break;
      case "description":
        $categories = $categories->orderBy("description", $sortDesc);
        break;
      case "products_count":
        $categories = $categories->orderBy("products_count", $sortDesc);
        break;
      default:
        $categories = $categories->latest();
    }
    return response()->json(new CategoryCollection($categories->paginate($request->input("per_page", 12))));
  }

  public function store(CategoryRequest $request)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('category.create')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
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
    $user = auth()->user();
    if (!$user || $user->cannot('category.update')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    try {
      Category::findOrFail($id)->update($request->validated());
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
    if (!$user || $user->cannot('category.update')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    try {
      $category = Category::findOrFail($id);
      return response()->json([
        'category' => $category
      ]);
    } catch (\Exception $exception) {
      return response()->json([
        "success" => false,
        "errors" => $exception->getMessage()
      ], 422);
    }
  }

  public function destroy(Category $category)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('category.delete')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $category->delete();
    return response()->noContent();
  }
}
