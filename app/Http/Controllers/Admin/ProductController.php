<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Admin\ProductCollection;

class ProductController extends Controller
{
  public function index(Request $request)
  {
    $user = auth()->user();
    if (!$user || !$user->can('product.access')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $data = Product::search($request->input('q'), "title")
      ->with(["brand:id,name", "category:id,name"])
      ->withCount('orders');
    $sortBy = $request->input("sortBy");
    $sortDesc = $request->input("sortDesc") == "false" ? "asc" : "desc";
    switch ($sortBy) {
      case "id":
        $data = $data->orderBy("id", $sortDesc);
        break;
      case "title":
        $data = $data->orderBy("title", $sortDesc);
        break;
      case "price":
        $data = $data->orderBy("price", $sortDesc);
        break;
      case "quantity":
        $data = $data->orderBy("quantity", $sortDesc);
        break;
      case "orders_count":
        $data = $data->orderBy("orders_count", $sortDesc);
        break;
      default:
        $data = $data->latest();
    }
    return response()->json(
      new ProductCollection($data->paginate($request->input('per_page', 12)))
    );
  }

  public function store(ProductRequest $request)
  {
    $user = auth()->user();
    if (!$user || !$user->can('product.create')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    try {
      DB::beginTransaction();
      if (!$request->hasFile("featured_image")) {
        return response()->json([
          "success" => false,
          "errors" => [
            "featured_image" => "Ảnh đại diện không được trống"
          ]
        ], 422);
      }
      $filename = pathinfo($request->file('featured_image')->getClientOriginalName(), PATHINFO_FILENAME);
      $extension = $request->file("featured_image")->extension();
      $final_name = $filename . "_" . date('mdYHis') . "_" . uniqid() . "." . $extension;
      $filePath = $request->file("featured_image")->storeAs(
        '/public/featured_image',
        $final_name
      );
      $url = Storage::url($filePath);
      $product = Product::create(
        array_merge(
          $request->validated(),
          ["featured_image" => $url]
        )
      );
      DB::commit();
      return response()->json([
        "product" => $product
      ]);
    } catch (\Exception $exception) {
      DB::rollBack();
      dd($exception);
      return response()->json([
        "success" => false,
        "errors" => $exception->getMessage()
      ], 422);
    }
  }

  public function update($id, ProductRequest $request)
  {
    $user = auth()->user();
    if (!$user || !$user->can('product.update')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    try {
      DB::beginTransaction();
      $data = $request->validated();
      if ($request->file('featured_image')) {
        $filename = pathinfo($request->file('featured_image')->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $request->file("featured_image")->extension();
        $final_name = $filename . "_" . date('mdYHis') . "_" . uniqid() . "." . $extension;
        $filePath = $request->file("featured_image")->storeAs(
          '/public/featured_image',
          $final_name
        );
        $url = Storage::url($filePath);
        $data = array_merge(
          $request->validated(),
          ["featured_image" => $url]
        );
      }
      $product = Product::findOrFail($id)->update($data);
      DB::commit();
      return response()->json([
        "product" => $product
      ]);
    } catch (\Exception $exception) {
      DB::rollBack();
      return response()->json([
        "success" => false,
        "errors" => $exception->getMessage()
      ], 422);
    }
  }

  public function show(Product $product)
  {
    $user = auth()->user();
    if (!$user || !$user->can('product.view')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    return response()->json([
      'product' => $product
    ]);
  }

  public function delete(Product $product)
  {
    $user = auth()->user();
    if (!$user || !$user->can('product.delete')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $product->delete();
    return response()->noContent();
  }
}
