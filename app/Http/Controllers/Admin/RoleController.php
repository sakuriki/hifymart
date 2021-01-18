<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\RoleRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Admin\RoleCollection;

class RoleController extends Controller
{
  /**
   * Return the franchises.
   */
  public function index(Request $request)
  {
    $user = auth()->user();
    if (!$user || !$user->can('role.access')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $roles = Role::where('name', 'LIKE', "%{$request->input('q')}%")
      ->select(['id', 'name', 'slug', 'description']);
    $sortBy = $request->input("sortBy");
    $sortDesc = $request->input("sortDesc") == "false" ? "asc" : "desc";
    switch ($sortBy) {
      case "id":
        $roles = $roles->orderBy("id", $sortDesc);
        break;
      case "name":
        $roles = $roles->orderBy("name", $sortDesc);
        break;
      case "description":
        $roles = $roles->orderBy("description", $sortDesc);
        break;
      case "permissions_count":
        $roles = $roles->orderBy("permissions_count", $sortDesc);
        break;
      default:
        $roles = $roles->oldest('id');
    }
    // return $posts->paginate($request->input('limit', 20));
    if ($request->input('per_page')) {
      $roles = $roles
        ->withCount("permissions");
      // return $roles->paginate($request->input('per_page'));
      $new = new RoleCollection($roles->paginate($request->input('per_page')));
    } else {
      $new = [
        "roles" => $roles->select(["id", "name"])->get()
      ];
    }
    return response()->json($new);
    // return response()->json($posts);
  }
  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Role $role)
  {
    $user = auth()->user();
    if (!$user || !$user->can('role.update')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $validator = Validator::make($request->all(), [
      'name' => 'required|string',
      'description' => 'nullable|string',
      'permissions' => 'nullable|array'
    ]);
    if ($validator->fails()) {
      return response()->json(['msg' => $validator->errors()], 404);
    }
    $role->update($request->only([
      'name',
      'description'
    ]));
    $role->permissions()->sync($request->input("permissions"));
    return response()->json([
      'role' => $role
    ]);
  }
  /**
   * Store a newly created resource in storage.
   */
  public function store(RoleRequest $request)
  {
    $user = auth()->user();
    if (!$user || !$user->can('role.create')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    try {
      $role = Role::create($request->validated());
      return response()->json([
        "role" => $role
      ]);
    } catch (\Exception $exception) {
      return response()->json([
        "success" => false,
        "errors" => $exception->getMessage()
      ], 422);
    }
  }
  /**
   * Return the specified resource.
   */
  public function show(Role $role)
  {
    $user = auth()->user();
    if (!$user || !$user->can('role.view')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $role->loadMissing("permissions");
    return response()->json([
      'role' => collect($role)->replace(["permissions" => $role->permissions->pluck("id")])
    ]);
  }
  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Role $role): Response
  {
    $user = auth()->user();
    if (!$user || !$user->can('role.delete')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $role->delete();
    return response()->noContent();
  }
}
