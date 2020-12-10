<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('products', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('brand_id')->unsigned()->nullable();
      $table->integer('category_id')->unsigned()->nullable();
      $table->string('title');
      $table->string('slug')->unique();
      $table->text('description');
      $table->integer('price');
      $table->text('featured_image');
      $table->timestamps();

      $table->index('title');
      $table->foreign('brand_id')->references('id')->on('brands')->onDelete('set null');
      $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('products');
  }
}
