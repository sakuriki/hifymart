<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\ProductCollection;

class ProductController extends Controller
{
  public function index(Request $request)
  {
    // return Product::select(['id', 'brand_id', 'category_id', 'name', 'slug', 'description', 'price', 'sale_off_price', 'featured_image'])
    //   ->selectRaw('FLOOR(100-(products.sale_off_price/products.price*100)) as sale_off_percent')
    //   ->orderBy('sale_off_percent', 'desc')
    //   ->paginate(12);
    $data = Product::search($request->input('q'), "name")
      ->select(['id', 'brand_id', 'category_id', 'name', 'slug', 'description', 'price', 'sale_off_price', 'sale_off_quantity', 'quantity', 'featured_image'])
      ->selectRaw('FLOOR(100-(products.sale_off_price/products.price*100)) as sale_off_percent')
      ->withCount("orders");
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
      case "sale_off_percent":
        $data = $data->orderBy("sale_off_percent", $sortDesc);
        break;
      case "random":
        $data = $data->inRandomOrder();
      default:
        $data = $data->latest();
    }
    if ($request->input("onsale")) {
      $data = $data->onSale();
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
