<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSaleOffToProductsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('products', function (Blueprint $table) {
      $table->integer('sale_off_price')->after('price')->unsigned()->nullable();
      $table->integer('sale_off_quantity')->after('sale_off_price')->unsigned()->nullable();
      $table->timestamp('sale_off_start')->after('sale_off_quantity')->nullable();
      $table->timestamp('sale_off_end')->after('sale_off_start')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('products', function (Blueprint $table) {
      $table->dropColumn('sale_off_price');
      $table->dropColumn('sale_off_start');
      $table->dropColumn('sale_off_end');
      $table->dropColumn('sale_off_quantity');
    });
  }
}
