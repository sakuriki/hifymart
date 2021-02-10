<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
  public function __invoke()
  {
    $settings = Setting::all()->keyBy('name');
    $keyed = $settings->mapWithKeys(function ($item) {
      return [$item['name'] => $item['value']];
    });
    return response()->json([
      'data' => $keyed->all()
    ]);
  }
}
