<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tag;
use App\Models\Product;
use Illuminate\Support\Str;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Admin\ProductCollection;

class ProductController extends Controller
{
  public function index(Request $request)
  {
    $user = Auth::user();
    if (!$user || $user->cannot('product.access')) {
      return response()->json([
        'code' => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $data = Product::search($request->input('q'), "name")
      ->select(['id', 'name', 'price', 'quantity', 'brand_id', 'category_id'])
      ->with(["brand:id,name", "category:id,name"])
      ->withCount('orders');
    $sortBy = $request->input("sortBy");
    $sortDesc = $request->input("sortDesc") == "false" ? "asc" : "desc";
    switch ($sortBy) {
      case "id":
        $data = $data->orderBy("id", $sortDesc);
        break;
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
      case 'views_count':
        $list = RedisManager::zRevRangeByScore("products_visits", "+inf", "-inf");
        $ids_ordered = implode(',', $list);
        $data = $data->whereIn('id', $list)
          ->orderByRaw("FIELD(id, $ids_ordered)");
        break;
      default:
        $data = $data->latest();
    }
    $data = $data->paginate($request->input('per_page', 12));
    $data->setCollection($data->getCollection()->makeHidden(['brand_id', 'category_id']));
    return response()->json(
      new ProductCollection($data)
    );
  }

  public function store(ProductRequest $request)
  {
    $user = Auth::user();
    if (!$user || $user->cannot('product.create')) {
      return response()->json([
        'code' => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    try {
      DB::beginTransaction();
      if (!$request->hasFile("featured_image")) {
        return response()->json([
          'errors' => [
            'featured_image' => [
              'Ảnh đại diện không được trống'
            ]
          ]
        ], 422);
      }
      $featured_image = $this->uploadOne($request->featured_image, '/public/products/featured_image');
      $url = Storage::url($featured_image);
      $product = Product::create(
        array_merge(
          $request->validated(),
          ["featured_image" => $url]
        )
      );
      $tagsId = collect($request->tags)->map(function ($tag) {
        return Tag::firstOrCreate(['name' => $tag])->id;
      });
      $product->tags()->sync($tagsId);
      if ($request->hasFile('images')) {
        $images = [];
        foreach ($request->file('images') as $image) {
          $imagePath = $this->uploadOne($image, '/public/products/product_images/' . $product->id);
          $url = Storage::url($imagePath);
          $images[] = new ProductImage([
            'url' => $url,
          ]);
        }
        $product->images()->saveMany($images);
      }
      DB::commit();
      return response()->json([
        "product" => $product
      ]);
    } catch (\Exception $exception) {
      DB::rollBack();
      dd($exception);
      return response()->json([
        'errors' => [
          'error' => [
            $exception->getMessage()
          ]
        ]
      ], 422);
    }
  }

  public function update($id, ProductRequest $request)
  {
    $user = Auth::user();
    if (!$user || $user->cannot('product.update')) {
      return response()->json([
        'code' => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    try {
      DB::beginTransaction();
      $data = $request->validated();
      $product = Product::findOrFail($id);
      if ($request->file('featured_image')) {
        if ($product->featured_image) {
          Storage::delete(Str::replaceFirst("storage", "public", $product->featured_image));
        }
        $featured_image = $this->uploadOne($request->featured_image, '/public/products/featured_image');
        $url = Storage::url($featured_image);
        $data = array_merge(
          $request->validated(),
          ["featured_image" => $url]
        );
      }
      $product->update($data);
      $tagsId = collect($request->tags)->map(function ($tag) {
        return Tag::firstOrCreate(['name' => $tag])->id;
      });
      $product->tags()->sync($tagsId);
      if ($request->hasFile('images')) {
        $images = [];
        foreach ($request->file('images') as $image) {
          $imagePath = $this->uploadOne($image, '/public/products/product_images/' . $product->id);
          $url = Storage::url($imagePath);
          $images[] = new ProductImage([
            'url' => $url,
          ]);
        }
        $product->images()->saveMany($images);
      }
      DB::commit();
      return response()->json([
        "product" => $product
      ]);
    } catch (\Exception $exception) {
      DB::rollBack();
      return response()->json([
        'errors' => [
          'error' => [
            $exception->getMessage()
          ]
        ]
      ], 422);
    }
  }

  public function show($id)
  {
    $user = Auth::user();
    if (!$user || $user->cannot('product.view')) {
      return response()->json([
        'code' => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $product = Product::select(['id', 'brand_id', 'category_id', 'tax_id', 'name', 'slug', 'description', 'content', 'price', 'quantity', 'sale_off_price', 'sale_off_quantity', 'sale_off_start', 'sale_off_end', 'featured_image'])
      ->with(['tags:id,name', 'images:id,product_id,url'])
      ->findOrFail($id);
    // $product = $product->load(['tags:id,name', 'images:id,product_id,url']);
    $tagNames = $product->tag_names;
    $product = $product->unsetRelation('tags')->setRelation('tags', $tagNames);
    return response()->json([
      'product' => $product
    ]);
  }

  public function destroy(Product $product)
  {
    $user = Auth::user();
    if (!$user || $user->cannot('product.delete')) {
      return response()->json([
        'code' => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    Storage::delete(Str::replaceFirst("storage", "public", $product->featured_image));
    $product->delete();
    return response()->noContent();
  }


  public function bulkDestroy(Request $request)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('product.delete')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    Product::whereIn('id', $request->listIds)->delete();
    return response()->noContent();
  }

  private function uploadOne(UploadedFile $file, $folder = null, $filename = null)
  {
    $name = !is_null($filename)
      ? $filename
      : pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

    return $file->storeAs(
      $folder,
      $name . "_" . date('mdYHis') . "_" . uniqid() . "." . $file->getClientOriginalExtension()
    );
  }

  /**
   * @param null $path
   * @param string $disk
   */
  private function deleteOne($path = null, $disk = 'public')
  {
    Storage::disk($disk)->delete($path);
  }
}
