<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
  public function __invoke()
  {
    $settings = Cache::rememberForever('settings', function () {
      return Setting::all();
    });
    $keyed = $settings->keyBy('name')->mapWithKeys(function ($item) {
      if ($item['name'] == 'slides') {
        $item['value'] = json_decode($item['value']);
      }
      return [$item['name'] => $item['value']];
    });
    return response()->json([
      'data' => $keyed->all()
    ]);
  }
}
