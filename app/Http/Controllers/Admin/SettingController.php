<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\SettingRequest;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
  public function __invoke(SettingRequest $request)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('setting')) {
      return response()->json([
        'errors' => [
          'You are unauthorized to access this resource'
        ]
      ], 401);
    }
    Cache::forget('settings');
    foreach ($request->validated() as $key => $value) {
      DB::table('settings')
        ->where('name', $key)
        ->update(['value' => $value]);
    }
    return response()->noContent();
  }
}
