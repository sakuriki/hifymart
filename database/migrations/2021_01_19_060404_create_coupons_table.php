<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('coupons', function (Blueprint $table) {
      $table->increments('id');
      $table->string('code', 32)->unique();
      $table->unsignedInteger('value');
      $table->unsignedInteger('number')->nullable();
      $table->unsignedInteger('min')->nullable();
      $table->unsignedInteger('max')->nullable();
      $table->boolean('is_percent')->default(0);
      $table->timestamp('starts_at')->nullable();
      $table->timestamp('expires_at')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('coupons');
  }
}
