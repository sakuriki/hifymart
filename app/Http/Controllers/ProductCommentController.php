<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\Comment;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductCommentController extends Controller
{
  public function show($id, Request $request)
  {
    $query_comments = Product::findOrfail($id)
      ->comments()
      ->with(['user:id,name', 'replies'])
      ->latest();
    $ref_comments = $query_comments;
    $total_comments = $ref_comments->count();
    $comments = $query_comments
      ->offset($request->input('offset', 0))
      ->limit($request->input('page_size', 12))
      ->get();
    return response()->json([
      'comments' => $comments,
      'total' => $total_comments
    ]);
  }

  public function showreply($id, Request $request)
  {
    $query_comments = Comment::findOrfail($id)
      ->replies()
      ->with('user:id,name')
      ->latest();
    $ref_comments = $query_comments;
    $total_comments = $ref_comments->count();
    $comments = $query_comments
      ->offset($request->input('offset', 0))
      ->limit($request->input('page_size', 12))
      ->get();
    return response()->json([
      'comments' => $comments,
      'total' => $total_comments
    ]);
  }

  public function store(Request $request)
  {
    try {
      $validator = Validator::make($request->all(), [
        'content' => 'required|string|min:10|max:2000',
        'name' => 'nullable|string|max:255',
        'phone' => 'nullable|numeric',
        'email' => 'nullable|string|email|max:255',
        'parent_id' => 'nullable|exists:comments,id|numeric',
        'product_id' => 'required|exists:products,id|numeric'
      ]);
      if ($validator->fails()) throw new \Exception($validator->errors()->all()[0]);
      // $comment = Comment::create($request->validated());
      $comment = new Comment;
      $comment->content = $request->input('content');
      $comment->name = $request->input('name');
      $comment->phone = $request->input('phone');
      $comment->email = $request->input('email');
      $comment->user()->associate($request->user());
      if ($request->input('parent_id')) {
        $parent = Comment::find($request->input('parent_id'));
        $parent_id = $parent->parent_id ? $parent->parent_id : $request->input('parent_id');
      }
      $comment->parent_id = $parent_id ?? null;
      $product = Product::find($request->input('product_id'));
      $product->comments()->save($comment);
    } catch (\Exception $exception) {
      // dd($exception);
      return response()->json([
        'errors' => [
          'error' => [
            $exception->getMessage()
          ]
        ]
      ], 422);
    };
    if (!$request->input('parent_id')) {
      $comment->replies = [];
    }
    $comment->replies_count = 0;
    return response()->json([
      'success' => true,
      'comment' => $comment
    ], 200);
  }

  public function update(Comment $comment, Request $request)
  {
    try {
      $validator = Validator::make($request->all(), [
        'content' => 'required|string|min:10|max:2000'
      ]);
      if ($validator->fails()) throw new \Exception($validator->errors()->all()[0]);
      if ($comment->user_id == $request->user()->id) {
        $comment->update(['content' => $request->input('content')]);
      } else {
        throw new \Exception("Bạn không có quyền sửa bình luận này!");
      }
    } catch (\Exception $exception) {
      // dd($exception);
      return response()->json([
        'errors' => [
          'error' => [
            $exception->getMessage()
          ]
        ]
      ], 422);
    };
    return response()->noContent();
  }

  public function destroy(Comment $comment, Request $request)
  {
    try {
      if ($comment->user_id == $request->user()->id) {
        $comment->delete();
      } else {
        throw new \Exception("Bạn không có quyền xoá bình luận này!");
      }
    } catch (\Exception $exception) {
      return response()->json([
        'errors' => [
          'error' => [
            $exception->getMessage()
          ]
        ]
      ]);
    };
    return response()->noContent();
  }
}
