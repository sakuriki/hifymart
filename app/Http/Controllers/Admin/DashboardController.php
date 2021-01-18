<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
  public function __invoke()
  {
    $user = auth()->user();
    if (!$user || $user->cannot('dashboard')) {
      return response()->json([
        'code'   => 401,
        'response' => 'You are unauthorized to access this resource'
      ]);
    }
    $count = DB::select("SELECT (SELECT COUNT(*) FROM `products`) as `products`, (SELECT COUNT(*) FROM `orders`) as `orders`, (SELECT COUNT(*) FROM `users`) as `users`")[0];
    $lastMonth = Order::select([
      DB::raw('count(id) as `count`'),
      DB::raw('SUM(billing_total) as `amount`'),
      DB::raw('DATE(created_at) as `date`'),
      // DB::raw('DATE_FORMAT(created_at, "%d/%m") as `day`'),
    ])
      ->groupBy('date')
      ->whereDate('created_at', '>=', Carbon::now()->subDays(30))
      ->whereDate('created_at', '<=', Carbon::now()) // limit dev seeder có order trong tương lai
      ->get();
    $lastYear = Order::select([
      DB::raw('count(id) as `count`'),
      DB::raw('SUM(billing_total) as `amount`'),
      // DB::raw('DATE(created_at) as `date`'),
      // DB::raw('MONTH(created_at) as `month`'),
      DB::raw('DATE_FORMAT(created_at, "%Y-%m") as `date`'),
    ])
      ->groupBy('date')
      ->whereDate('created_at', '>=', Carbon::now()->subYear())
      ->get();
    return response()->json([
      "total_count" => $count,
      "last_month" => $lastMonth,
      "last_year" => $lastYear
    ]);
  }
}
