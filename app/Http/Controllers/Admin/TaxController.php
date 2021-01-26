<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tax;
use Illuminate\Http\Request;
use App\Http\Requests\TaxRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaxCollection;

class TaxController extends Controller
{
  public function index(Request $request)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('tax.access')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $taxes = Tax::where('name', 'LIKE', "%{$request->input('q')}%")
      ->select(['id', 'name', 'value'])
      ->withCount('products');
    $sortBy = $request->input("sortBy");
    $sortDesc = $request->input("sortDesc") == "false" ? "asc" : "desc";
    switch ($sortBy) {
      case "id":
        $taxes = $taxes->orderBy("id", $sortDesc);
        break;
      case "name":
        $taxes = $taxes->orderBy("name", $sortDesc);
        break;
      case "value":
        $taxes = $taxes->orderBy("value", $sortDesc);
        break;
      case "products_count":
        $taxes = $taxes->orderBy("products_count", $sortDesc);
        break;
      default:
        $taxes = $taxes->latest();
    }
    return response()->json(new TaxCollection($taxes->paginate($request->input("per_page", 12))));
  }

  public function store(TaxRequest $request)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('tax.create')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    try {
      $tax = Tax::create($request->validated());
      return response()->json([
        "tax" => $tax
      ]);
    } catch (\Exception $exception) {
      return response()->json([
        "success" => false,
        "errors" => $exception->getMessage()
      ], 422);
    }
  }

  public function update($id, TaxRequest $request)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('tax.update')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    try {
      Tax::findOrFail($id)->update($request->validated());
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
    if (!$user || $user->cannot('tax.view')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    try {
      $tax = Tax::findOrFail($id);
      return response()->json([
        "tax" => $tax
      ]);
    } catch (\Exception $exception) {
      return response()->json([
        "success" => false,
        "errors" => $exception->getMessage()
      ], 422);
    }
  }

  public function destroy(Tax $tax)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('tax.delete')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $tax->delete();
    return response()->noContent();
  }
}
