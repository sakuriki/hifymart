<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {
    User::firstOrCreate(
      ['email' => 'admin@admin.admin'],
      [
        'name' => 'ADMIN',
        'password' => Hash::make('12345678'),
        'email_verified_at' => now()
      ]
    );
  }
}
