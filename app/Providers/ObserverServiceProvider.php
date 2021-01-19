<?php

namespace App\Providers;

use App\Models\Tag;
use App\Models\Brand;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Category;
use App\Observers\TagObserver;
use App\Observers\BrandObserver;
use App\Observers\CouponObserver;
use App\Observers\ProductObserver;
use App\Observers\CategoryObserver;
use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
{
  /**
   * Bootstrap the application services.
   */
  public function boot(): void
  {
    Tag::observe(TagObserver::class);
    Brand::observe(BrandObserver::class);
    Coupon::observe(CouponObserver::class);
    Product::observe(ProductObserver::class);
    Category::observe(CategoryObserver::class);
  }
}
