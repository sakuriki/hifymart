<?php

namespace App\Providers;

use App\Models\Product;
use App\Observers\ProductObserver;
use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
{
  /**
   * Bootstrap the application services.
   */
  public function boot(): void
  {
    Product::observe(ProductObserver::class);
  }
}
