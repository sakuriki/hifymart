<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceToOrderProductTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('order_product', function (Blueprint $table) {
      $table->unsignedInteger('price')->after('quantity');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('order_product', function (Blueprint $table) {
      $table->dropColumn('price');
    });
  }
}
