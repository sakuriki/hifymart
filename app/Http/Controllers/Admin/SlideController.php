<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SlideController extends Controller
{
  public function index()
  {
    $user = auth()->user();
    if (!$user || $user->cannot('setting')) {
      return response()->json([
        'errors' => [
          'You are unauthorized to access this resource'
        ]
      ], 401);
    }
    $slides = Setting::where('name', 'slides')->withCasts([
      'value' => 'array'
    ])->firstOrFail()->value;
    return response()->json([
      'slides' => $slides
    ]);
  }

  public function store(Request $request)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('setting')) {
      return response()->json([
        'errors' => [
          'You are unauthorized to access this resource'
        ]
      ], 401);
    }
    try {
      if ($request->hasFile('image')) {
        DB::beginTransaction();
        $slides = Setting::where('name', 'slides')->withCasts([
          'value' => 'array'
        ])->firstOrFail();
        $images = collect($slides->value);
        $imagePath = $this->uploadOne($request->file('image'), '/public/slides');
        $url = Storage::url($imagePath);
        $images->push([
          'title' => $request->title,
          'url' => $url,
          'order' => (int) $request->order
        ]);
        $slides->update([
          'value' => $images
        ]);
        DB::commit();
        Cache::forget('settings');
        return response()->json([
          'slides' => $slides->value
        ]);
      }
      return response()->json([
        'errors' => [
          'error' => [
            'Image required'
          ]
        ]
      ], 422);
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

  public function update(Request $request)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('setting')) {
      return response()->json([
        'errors' => [
          'You are unauthorized to access this resource'
        ]
      ], 401);
    }
    try {
      DB::beginTransaction();
      Setting::where('name', 'slides')
        ->firstOrFail()
        ->update([
          'value' => $request->slides
        ]);
      DB::commit();
      Cache::forget('settings');
      return response()->noContent();
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

  public function destroy(Request $request)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('setting')) {
      return response()->json([
        'errors' => [
          'You are unauthorized to access this resource'
        ]
      ], 401);
    }
    try {
      $slides = Setting::where('name', 'slides')->withCasts([
        'value' => 'array'
      ])->firstOrFail();
      $filter = collect($slides->value)->filter(function ($item) use ($request) {
        if ($item['url'] != $request->url) {
          return $item;
        } else {
          Storage::delete(Str::replaceFirst("storage", "public", $item['url']));
        }
      });
      $slides->update([
        'value' => $filter->values()->all()
      ]);
      Cache::forget('settings');
      return response()->noContent();
    } catch (\Exception $exception) {
      return response()->json([
        'errors' => [
          'error' => [
            $exception->getMessage()
          ]
        ]
      ], 422);
    }
  }
}
