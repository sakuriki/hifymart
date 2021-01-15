<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
  public function index(Request $request)
  {
    $brands = Tag::select(['id', 'name'])->get()->pluck('name');
    return response()->json([
      'tags' => $brands
    ]);
  }
}
