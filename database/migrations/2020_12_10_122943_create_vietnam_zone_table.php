<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVietnamZoneTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   * @throws Exception
   */
  public function up()
  {

    Schema::create('provinces', function (Blueprint $table) {
      $table->increments('id');
      $table->string('name');
      $table->string('gso_id');
    });

    Schema::create('districts', function (Blueprint $table) {
      $table->increments('id');
      $table->string('name');
      $table->string('gso_id');
      $table->integer('province_id')->unsigned();

      $table->foreign('province_id')
        ->references('id')
        ->on('provinces')
        ->cascadeOnDelete();
    });

    Schema::create('wards', function (Blueprint $table) {
      $table->increments('id');
      $table->string('name');
      $table->string('gso_id');
      $table->integer('district_id')->unsigned();

      $table->foreign('district_id')
        ->references('id')
        ->on('districts')
        ->cascadeOnDelete();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   * @throws Exception
   */
  public function down()
  {
    Schema::dropIfExists('wards');
    Schema::dropIfExists('districts');
    Schema::dropIfExists('provinces');
  }
}
