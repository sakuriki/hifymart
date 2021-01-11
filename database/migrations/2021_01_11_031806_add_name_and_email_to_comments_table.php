<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNameAndEmailToCommentsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('comments', function (Blueprint $table) {
      $table->renameColumn('comment', 'content');
      $table->string('name')->after('parent_id')->nullable();
      $table->string('phone')->after('name')->nullable();
      $table->string('email')->after('phone')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('comments', function (Blueprint $table) {
      $table->renameColumn('content', 'comment');
      $table->dropColumn('name');
      $table->dropColumn('phone');
      $table->dropColumn('email');
    });
  }
}
