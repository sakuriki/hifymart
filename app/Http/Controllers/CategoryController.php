<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\District;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
  public function index(Request $request)
  {
    $categories = Category::select(['id', 'parent_id', 'name', 'slug'])
      ->with('childrenRecursive:id,parent_id,name,slug')
      ->whereNull('parent_id')
      ->get();
    $categories->map(function ($category) use ($request) {
      $category->disabled = $request->exclude == $category->id ? true : false;
      return $category;
    });
    // return $categories;
    return response()->json([
      'categories' => $this->categoryRecusive($categories, $request->exclude)
    ]);
  }

  public function categoryRecusive($categories, $exclude = null, $text = '')
  {
    $data = [];
    foreach ($categories as $category) {
      $data[] = [
        'id' => $category->id,
        'name' => $text . $category->name,
        'parent_id' => $category->parent_id,
        'slug' => $category->slug,
        'disabled' => $exclude == $category->id ? true : false
      ];
      if ($category->childrenRecursive) {
        $data = array_merge($data, $this->categoryRecusive($category->childrenRecursive, $exclude, $text . '--'));
      }
    }
    return $data;
  }

  public function show($slug, Request $request)
  {
    $category = Category::where('slug', $slug)
      ->select('id', 'name', 'description')
      ->firstOrFail();
    return response()->json([
      'category' => $category
    ]);
  }
}
