<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Admin\TagCollection;

class TagController extends Controller
{
  /**
   * Return the tags.
   */
  public function index(Request $request)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('tag.access')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $tags = Tag::where('name', 'LIKE', "%{$request->input('q')}%");
    $sortBy = $request->input("sortBy");
    $sortDesc = $request->input("sortDesc") == "false" ? "asc" : "desc";
    switch ($sortBy) {
      case "id":
        $tags = $tags->orderBy("id", $sortDesc);
        break;
      case "name":
        $tags = $tags->orderBy("name", $sortDesc);
        break;
      case "products_count":
        $tags = $tags->orderBy("products_count", $sortDesc);
        break;
      default:
        $tags = $tags->latest();
    }
    if ($request->input('per_page')) {
      $tags = $tags
        ->withCount("products");
      $new = new TagCollection($tags->paginate($request->input('per_page')));
    } else {
      $new = [
        "tags" => $tags->get()->makeHidden(['slug', 'created_at', 'updated_at'])
      ];
    }
    return response()->json($new);
  }
  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Tag $tag)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('tag.update')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $validator = Validator::make($request->all(), [
      'name' => 'required|string'
    ]);
    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'errors' => $validator->errors()
      ], 404);
    }
    $tag->update($request->only([
      'name'
    ]));
    return $tag;
  }
  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('tag.create')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $validator = Validator::make($request->all(), [
      'name' => 'required|string'
    ]);
    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'errors' => $validator->errors()
      ], 404);
    }
    $tag = Tag::create($request->only([
      'name'
    ]));
    return $tag;
  }
  /**
   * Return the specified resource.
   */
  public function show(Tag $tag)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('tag.view')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    return response()->json([
      'tag' => $tag
    ]);
  }
  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Tag $tag): Response
  {
    $user = auth()->user();
    if (!$user || $user->cannot('tag.delete')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $tag->delete();
    return response()->noContent();
  }

  public function bulkDestroy(Request $request)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('tag.delete')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    Tag::whereIn('id', $request->listIds)->delete();
    return response()->noContent();
  }
}
