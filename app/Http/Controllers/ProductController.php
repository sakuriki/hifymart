<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\ProductCollection;

class ProductController extends Controller
{
  public function index(Request $request)
  {
    $data = Product::search($request->input('q'), "name")->withCount("orders");
    $sortBy = $request->input("sortBy");
    $sortDesc = $request->input("sortDesc") == "false" ? "asc" : "desc";
    switch ($sortBy) {
      case "name":
        $data = $data->orderBy("name", $sortDesc);
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
      new ProductCollection($data->paginate($request->input('per_page', 8)))
    );
  }

  public function show($slug, Request $request)
  {
    $product = Product::where("slug", $slug)->select(["id", "brand_id", "category_id", "name", "slug", "description", "price", "quantity", "featured_image"])->with(["brand:id,name,slug", "category:id,name,slug", "tags"])->firstOrFail();
    return response()->json([
      'product' => $product
    ]);
  }
}
