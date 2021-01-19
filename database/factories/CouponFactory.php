<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Carbon\Carbon;
use App\Models\Coupon;
use Faker\Generator as Faker;

$factory->define(Coupon::class, function (Faker $faker) {
  $is_percent = $faker->boolean(70);
  $min = null;
  $max = null;
  $starts_at = null;
  $expires_at = null;
  if ($is_percent) {
    $value = $faker->numberBetween(1, 30);
  } else {
    $value = $faker->numberBetween(10, 300) * 1000;
  }
  if ($faker->boolean(70)) {
    $min = $faker->numberBetween(10, 300) * 1000;
    $max = $faker->numberBetween($min / 1000, 400) * 1000;
  }
  if ($faker->boolean(70)) {
    $starts_at = $faker->dateTimeBetween('-1 years', '+1 year');
    $carbon = Carbon::instance($starts_at);
    $expires_at = $faker->dateTimeBetween($carbon->addMonth()->toDateTimeString(), $carbon->addMonths(3)->toDateTimeString());
  }
  $coupon = new Coupon();
  return [
    'code' => $coupon->generate(),
    'value' => $value,
    'number' => $faker->numberBetween(0, 99),
    'min' => $min,
    'max' => $max,
    'is_percent' => $is_percent,
    'starts_at' => $starts_at,
    'expires_at' => $expires_at,
  ];
});
