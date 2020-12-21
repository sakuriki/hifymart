<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use Illuminate\Support\Str;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
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
    $data = Product::search($request->input('q'), "name")
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
      // return $request->featured_image;
      // return $request->hasFile("featured_image") ? "haa" : "ho";
      if (!$request->hasFile("featured_image")) {
        return response()->json([
          "success" => false,
          "errors" => [
            "featured_image" => "Ảnh đại diện không được trống"
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
