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
    //   // ->orderBy('sale_off_percent', 'desc')
    //   ->where(function ($query) {
    //     $query->whereBetween('price', [0, 30000])
    //       ->orWhereBetween('sale_off_price', [0, 30000]);
    //   })
    //   // ->whereBetween('price', [0, 100000])
    //   ->orderBy('price', 'asc')
    //   ->paginate(12);
    $data = Product::search($request->input('q'), "name")
      ->select(['id', 'brand_id', 'category_id', 'name', 'slug', 'description', 'price', 'sale_off_price', 'sale_off_quantity', 'quantity', 'featured_image'])
      ->selectRaw('FLOOR(100-(products.sale_off_price/products.price*100)) as sale_off_percent')
      ->when($request->input("onsale"), function ($query) {
        $query->onSale();
      })
      ->when($request->input("range"), function ($query, $range) {
        $query->where(function ($query) use ($range) {
          $query->whereBetween('price', $range)
            ->orWhereBetween('sale_off_price', $range);
        });
      })
      ->when($request->input('brand'), function ($query, $id) {
        $query->where('brand_id', $id);
      })
      ->when($request->input('category'), function ($query, $id) {
        $query->where('category_id', $id);
      })
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
