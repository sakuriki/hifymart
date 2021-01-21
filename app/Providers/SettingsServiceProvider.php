<?php

namespace App\Providers;

use App\Models\Setting;
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
   * @param \Illuminate\Support\Facades\Cache $cache
   * @param \App\Models\Setting            $settings
   *
   * @return void
   */
  public function boot(Setting $settings)
  {
    $settings = Cache::rememberForever('settings', function () use ($settings) {
      return $settings->pluck('value', 'name')->all();
    });
    config()->set('settings', $settings);
  }
}
