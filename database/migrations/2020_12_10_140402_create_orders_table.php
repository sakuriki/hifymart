<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('orders', function (Blueprint $table) {
      $table->increments('id');
      $table->string('status');
      $table->integer('user_id')->unsigned()->nullable();
      $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
      $table->string('billing_email')->nullable();
      $table->string('billing_name')->nullable();
      $table->string('billing_address')->nullable();
      $table->string('billing_city')->nullable();
      $table->string('billing_province')->nullable();
      $table->string('billing_note')->nullable();
      $table->string('billing_phone')->nullable();
      $table->integer('billing_discount')->unsigned()->default(0);
      $table->string('billing_discount_code')->nullable();
      $table->integer('billing_subtotal')->unsigned();
      $table->integer('billing_tax')->unsigned();
      $table->integer('billing_total')->unsigned();
      $table->boolean('shipped')->default(false);
      $table->string('error')->nullable();
      $table->timestamps();
    });

    Schema::create('order_product', function (Blueprint $table) {
      $table->integer('product_id')->unsigned();
      $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
      $table->integer('order_id')->unsigned();
      $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
      $table->primary(['product_id', 'order_id']);
      $table->integer('quantity')->unsigned();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('order_product');
    Schema::dropIfExists('orders');
  }
}
