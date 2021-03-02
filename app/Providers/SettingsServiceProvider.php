<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   *
   * @return void
   */
  public function register()
  {
    //
  }

  /**
   * Bootstrap the application services.
   *
   *
   * @return void
   */
  public function boot()
  {
    if(!App::runningInConsole()) {
      $settings = Cache::rememberForever('settings', function () {
        return Setting::all();
      });
      config()->set('settings', $settings->pluck('value', 'name')->all());
    }
  }
}
