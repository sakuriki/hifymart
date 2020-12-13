<?php

namespace App\Http\Controllers\Admin;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PermissionController extends Controller
{
  public function index(Request $request)
  {
    $permissions = Permission::all()->groupBy('group');
    $arr = [];
    foreach ($permissions as $key => $value) {
      if (count($value) <= 1) {
        $arr[] = [
          "id" => $value->first()->id,
          "name" => $value->first()->name,
          "description" => $value->first()->description
        ];
      } else {
        $arr[] = [
          "id" => $key,
          "name" => $key,
          "description" => "Toàn quyền",
          "children" => $value
        ];
      }
    }
    // return;
    return response()->json(["permissions" => $arr]);
    // return DB::table('permissions')
    //   ->select(['id', 'name', 'slug', 'description', 'group'])
    //   ->get()
    //   ->groupBy('group');
  }
}
