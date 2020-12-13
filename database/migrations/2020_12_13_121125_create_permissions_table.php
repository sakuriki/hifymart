<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('permissions', function (Blueprint $table) {
      $table->increments('id');
      $table->string('name');
      $table->string('slug');
      $table->string('group');
      $table->string('description')->nullable();
      $table->timestamps();
    });

    Schema::create('permission_role', function (Blueprint $table) {
      $table->integer('permission_id')->unsigned();
      $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
      $table->integer('role_id')->unsigned();
      $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
      $table->primary(['permission_id', 'role_id']);
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('permission_role');
    Schema::dropIfExists('permissions');
  }
}
