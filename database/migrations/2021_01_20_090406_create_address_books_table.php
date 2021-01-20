<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressBooksTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('address_books', function (Blueprint $table) {
      $table->increments('id');
      $table->string('name');
      $table->string('address');
      $table->string('phone');
      $table->string('email');
      $table->unsignedInteger('user_id');
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
      $table->unsignedInteger('province_id')->nullable();
      $table->foreign('province_id')->references('id')->on('provinces')->onDelete('set null');
      $table->unsignedInteger('district_id')->nullable();
      $table->foreign('district_id')->references('id')->on('districts')->onDelete('set null');
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
    Schema::dropIfExists('address_books');
  }
}
