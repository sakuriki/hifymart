<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
  public function show($id, Request $request)
  {
    return response()->json([
      'user' => User::select(['id', 'name'])->findOrFail($id)
    ]);
  }
}
