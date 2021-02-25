<?php

namespace App\Http\Controllers\Admin;

use App\Models\Rating;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\RatingRequest;
use App\Http\Resources\RatingCollection;

class RatingController extends Controller
{
  public function index(Request $request)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('rating.access')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $ratings = Rating::where('review', 'LIKE', "%{$request->input('q')}%")
      ->select(['id', 'approved', 'rating', 'review', 'user_id'])
      ->with(['user:id,name']);
    $sortBy = $request->input("sortBy");
    $sortDesc = $request->input("sortDesc") == "false" ? "asc" : "desc";
    switch ($sortBy) {
      case "id":
        $ratings = $ratings->orderBy("id", $sortDesc);
        break;
      case "approved":
        $ratings = $ratings->orderBy("approved", $sortDesc);
        break;
      case "rating":
        $ratings = $ratings->orderBy("rating", $sortDesc);
        break;
      case "review":
        $ratings = $ratings->orderBy("review", $sortDesc);
        break;
      default:
        $ratings = $ratings->latest();
    }
    return response()->json(new RatingCollection($ratings->paginate($request->input("per_page", 12))));
  }

  // public function store(RatingRequest $request)
  // {
  // }

  public function update($id, RatingRequest $request)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('rating.update')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    try {
      Rating::findOrFail($id)->update($request->validated());
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
    if (!$user || $user->cannot('rating.view')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    try {
      $rating = Rating::findOrFail($id);
      return response()->json([
        "rating" => $rating
      ]);
    } catch (\Exception $exception) {
      return response()->json([
        "success" => false,
        "errors" => $exception->getMessage()
      ], 422);
    }
  }

  public function destroy(Rating $rating)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('rating.delete')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $rating->delete();
    return response()->noContent();
  }

  public function bulkDestroy(Request $request)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('rating.delete')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    Rating::whereIn('id', $request->listIds)->delete();
    return response()->noContent();
  }
}
