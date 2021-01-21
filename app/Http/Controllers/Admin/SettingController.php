<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{

  public function update(Setting $setting, Request $request)
  {
    $user = auth()->user();
    if (!$user || $user->cannot('setting.update')) {
      return response()->json([
        'errors' => [
          'You are unauthorized to access this resource'
        ]
      ], 401);
    }
    Cache::forget('settings');
    $setting->update($request->only([
      'name',
      'value'
    ]));
    return response()->json([
      'setting' => $setting
    ]);
  }
}
