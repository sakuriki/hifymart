<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
  public function index(Request $request)
  {
    $categories = Category::select(['id', 'parent_id', 'name', 'slug'])
      ->with('childrenRecursive:id,parent_id,name,slug')
      ->whereNull('parent_id')
      ->get();
    return response()->json([
      'categories' => $this->categoryRecusive($categories)
    ]);
  }

  public function categoryRecusive($categories, $text = '')
  {
    $data = [];
    foreach ($categories as $category) {
      $data[] = [
        'id' => $category->id,
        'name' => $text . $category->name,
        'parent_id' => $category->parent_id,
        'slug' => $category->slug
      ];
      if ($category->childrenRecursive) {
        $data = array_merge($data, $this->categoryRecusive($category->childrenRecursive, $text . '--'));
      }
    }
    return $data;
  }
}
