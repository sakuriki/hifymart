<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\UserCollection;

class UserController extends Controller
{
  public function index(Request $request)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('user.access')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $users = User::where('name', 'LIKE', "%{$request->input('q')}%")
      ->select(['id', 'name', 'email', 'phone'])
      ->withCount(['comments', 'orders', 'wishlists']);
    $sortBy = $request->input("sortBy");
    $sortDesc = $request->input("sortDesc") == "false" ? "asc" : "desc";
    switch ($sortBy) {
      case "id":
        $users = $users->orderBy("id", $sortDesc);
        break;
      case "name":
        $users = $users->orderBy("name", $sortDesc);
        break;
      case "email":
        $users = $users->orderBy("email", $sortDesc);
        break;
      case "phone":
        $users = $users->orderBy("phone", $sortDesc);
        break;
      case "comments_count":
        $users = $users->orderBy("comments_count", $sortDesc);
        break;
      case "orders_count":
        $users = $users->orderBy("orders_count", $sortDesc);
        break;
      case "wishlists_count":
        $users = $users->orderBy("wishlists_count", $sortDesc);
        break;
      default:
        $users = $users->latest();
    }
    return response()->json(new UserCollection($users->paginate($request->input('per_page', 12))));
  }

  public function store(UserRequest $request)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('user.create')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    try {
      $user = User::create($request->validated());
      return response()->json([
        "user" => $user
      ]);
    } catch (\Exception $exception) {
      return response()->json([
        "success" => false,
        "errors" => $exception->getMessage()
      ], 422);
    }
  }

  public function update($id, UserRequest $request)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('user.update')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    try {
      User::findOrFail($id)->update($request->validated());
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
    if (!$user || $user->cannot('user.view')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    try {
      $user = User::findOrFail($id);
      return response()->json([
        "user" => $user
      ]);
    } catch (\Exception $exception) {
      return response()->json([
        "success" => false,
        "errors" => $exception->getMessage()
      ], 422);
    }
  }

  public function destroy(User $user)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('user.delete')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $user->delete();
    return response()->noContent();
  }
}
