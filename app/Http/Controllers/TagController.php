<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
  public function index(Request $request)
  {
    $tags = Tag::select(['id', 'name', 'slug'])->get();
    if ($request->nameOnly) {
      return response()->json([
        'tags' => $tags->pluck('name')
      ]);
    }
    return response()->json([
      'tags' => $tags
    ]);
  }

  public function show($slug, Request $request)
  {
    $tag = Tag::where('slug', $slug)
      ->select('id', 'name', 'slug')
      ->firstOrFail();
    return response()->json([
      'tag' => $tag
    ]);
  }
}
