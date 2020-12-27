<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\Product as ProductResource;

class ProductController extends Controller
{
  public function index(Request $request)
  {
    // $data = Product::leftJoin('ratings', 'ratings.product_id', '=', 'products.id')
    //   ->select([
    //     'products.*',
    //     DB::raw('ROUND(AVG(rating)/0.5, 0)*0.5 as ratings_average')
    //   ])
    //   ->having(DB::raw('ROUND(AVG(rating)/0.5, 0)*0.5'), '>=', 3)
    //   ->groupBy('id')
    //   ->get();
    // return $data;
    // $a = Product::find(82);
    // $b = $a->averageRating();
    // return response()->json(collect($a)->merge(['averageRating' => (float) $b->first()]));
    $data = Product::search($request->input('q'), "name")
      ->leftJoin('ratings', 'ratings.product_id', '=', 'products.id')
      ->select([
        'products.id',
        'products.brand_id',
        'products.category_id',
        'products.name',
        'products.slug',
        'products.description',
        'products.price',
        'products.sale_off_price',
        'products.sale_off_quantity',
        'products.quantity',
        'products.featured_image',
        DB::raw('AVG(rating) as ratings_average')
        // DB::raw('ROUND(AVG(rating)/0.5, 0)*0.5 as ratings_average')
      ])
      ->groupBy('id')
      // ->selectRaw('FLOOR(100-(products.sale_off_price/products.price*100)) as sale_off_percent')
      ->when($request->input("onsale"), function ($query) {
        $query->onSale();
      })
      ->when($request->input("range"), function ($query, $range) {
        $query->where(function ($query) use ($range) {
          $query->whereBetween('price', $range)
            ->orWhereBetween('sale_off_price', $range);
        });
      })
      ->when($request->input('brands'), function ($query, $id) {
        $query->whereIn('brand_id', Arr::wrap($id));
      })
      ->when($request->input('category'), function ($query, $id) {
        $query->where('category_id', $id);
      })
      ->when($request->input('rating'), function ($query, $rating) {
        $query->having(DB::raw('AVG(rating)'), '>=', $rating);
        // $query->having(DB::raw('ROUND(AVG(rating)/0.5, 0)*0.5'), '>=', $rating);
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
      case "rating":
        $data = $data->orderBy("ratings_average", $sortDesc);
        break;
      case "random":
        $data = $data->inRandomOrder();
      default:
        $data = $data->orderBy('products.created_at', 'desc');
    }
    return response()->json(
      new ProductCollection($data->paginate($request->input('per_page', 8)))
    );
  }

  public function show($slug, Request $request)
  {
    $data = Cache::remember('cache_product_' . $slug, 60 * 60 * 24, function () use ($slug, $request) {
      $product = Product::where("slug", $slug)
        // ->leftJoin('ratings', 'ratings.product_id', '=', 'products.id')
        ->leftJoin('ratings', function ($query) {
          $query->on('ratings.product_id', '=', 'products.id')
            ->where('ratings.approved', 1);
        })
        ->select([
          'products.id',
          'products.brand_id',
          'products.category_id',
          'products.name',
          'products.slug',
          'products.description',
          'products.price',
          'products.sale_off_price',
          'products.sale_off_quantity',
          'products.quantity',
          'products.featured_image',
          // DB::raw('ROUND(AVG(rating)/0.5, 0)*0.5 as ratings_average')
          DB::raw('AVG(rating) as ratings_average')
        ])
        ->groupBy('id')
        ->with([
          "brand:id,name,slug",
          "images:url,product_id",
          "category:id,name,slug",
          "tags",
          "ratings" => function ($query) {
            $query->select('rating', 'product_id', DB::raw('count(*) as total'))
              ->where("approved", 1)
              ->groupBy('rating');
          }
        ])
        ->withCount(["ratings" => function ($query) {
          $query->where("approved", 1);
        }])
        ->firstOrFail();
      // return $product;
      $product->images->makeHidden(['product_id']);
      // $product->ratingsWithUser->makeHidden(['product_id', 'user_id', 'approved', 'created_at', 'updated_at']);
      $product->ratings->makeHidden(['product_id']);
      $product->ratings_average = (float) $product->ratings_average;
      return $product;
    });
    return response()->json([
      'product' => $data
    ]);
  }
}
