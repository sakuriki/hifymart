<?php

namespace App\Http\Controllers\Admin;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Http\Resources\Admin\CommentCollection;

class CommentController extends Controller
{
  public function index(Request $request)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('comment.access')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $comments = Comment::search($request->input('q'))
      ->select(['id', 'name', 'content', 'user_id'])
      ->with('user:id,name');
    $sortBy = $request->input("sortBy");
    $sortDesc = $request->input("sortDesc") == "false" ? "asc" : "desc";
    switch ($sortBy) {
      case "id":
        $comments = $comments->orderBy("id", $sortDesc);
        break;
      case "name":
        $comments = $comments->orderBy("name", $sortDesc);
        break;
      case "content":
        $comments = $comments->orderBy("content", $sortDesc);
        break;
      default:
        $comments = $comments->latest();
    }
    return response()->json(new CommentCollection($comments->paginate($request->input("per_page", 12))));
  }

  // public function store(CommentRequest $request)
  // {
  //   $user = auth()->user();
  //   if (!$user || $user->cannot('comment.create')) {
  //     return response()->json([
  //       'code'   => 401,
  //       'response' => 'You are unauthorized to access this resource'
  //     ]);
  //   }
  //   try {
  //     $comment = Comment::create($request->validated());
  //     return response()->json([
  //       "comment" => $comment
  //     ]);
  //   } catch (\Exception $exception) {
  //     return response()->json([
  //       "success" => false,
  //       "errors" => $exception->getMessage()
  //     ], 422);
  //   }
  // }

  public function update($id, CommentRequest $request)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('comment.update')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    try {
      Comment::findOrFail($id)->update($request->validated());
      return response()->noContent();
    } catch (\Exception $exception) {
      return response()->json([
        'errors' => [
          'error' => [
            $exception->getMessage()
          ]
        ]
      ], 422);
    }
  }

  public function show($id, Request $request)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('comment.view')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    try {
      $comment = Comment::findOrFail($id);
      return response()->json([
        "comment" => $comment
      ]);
    } catch (\Exception $exception) {
      return response()->json([
        'errors' => [
          'error' => [
            $exception->getMessage()
          ]
        ]
      ], 422);
    }
  }

  public function destroy(Comment $comment)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('comment.delete')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $comment->delete();
    return response()->noContent();
  }

  public function bulkDestroy(Request $request)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('comment.delete')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    Comment::whereIn('id', $request->listIds)->delete();
    return response()->noContent();
  }
}
